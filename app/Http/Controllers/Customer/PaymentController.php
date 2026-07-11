<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = auth()->user()
            ->payments()
            ->with(['booking.vehicle', 'booking.servicePackage', 'booking.workshop'])
            ->when($request->filled('reference'), fn($query) => $query->where('payment_reference', 'like', '%' . $request->reference . '%'))
            ->when($request->filled('method'), fn($query) => $query->where('method', $request->method))
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.payments.index', [
            'payments' => $payments,
            'methods' => Payment::METHODS,
            'statuses' => Payment::STATUSES,
        ]);
    }

    public function create(Booking $booking)
    {
        $this->ensureOwner($booking);
        $booking->load(['vehicle', 'servicePackage', 'workshop', 'payment']);

        if ($booking->payment) {
            return redirect()->route('customer.payments.show', $booking->payment)
                ->with('error', 'This booking has already been paid.');
        }

        if (! in_array($booking->status, ['Approved', 'Completed'], true)) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'Payment is available after the booking is approved by admin.');
        }

        $discountPercentage = auth()->user()->subscriptionDiscountPercentage();
        $discountAmount = round(((float) $booking->total_price * $discountPercentage) / 100, 2);
        $payableAmount = max((float) $booking->total_price - $discountAmount, 0);

        return view('customer.payments.create', compact('booking', 'discountPercentage', 'discountAmount', 'payableAmount'));
    }

    public function store(Request $request, Booking $booking)
    {
        $this->ensureOwner($booking);
        $booking->load(['payment']);

        if ($booking->payment) {
            return redirect()->route('customer.payments.show', $booking->payment)
                ->with('error', 'This booking has already been paid.');
        }

        if (! in_array($booking->status, ['Approved', 'Completed'], true)) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'Payment can only be made after admin approval.');
        }

        $data = $request->validate([
            'method' => ['required', Rule::in(Payment::METHODS)],
            'payer_name' => ['required', 'string', 'max:150'],
            'payer_email' => ['nullable', 'email', 'max:255'],
            'card_last_four' => ['nullable', 'digits:4'],
            'transaction_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $discountPercentage = auth()->user()->subscriptionDiscountPercentage();
        $amount = (float) $booking->total_price;
        $discountAmount = round(($amount * $discountPercentage) / 100, 2);
        $totalPaid = max($amount - $discountAmount, 0);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'payment_reference' => $this->makeReference(),
            'method' => $data['method'],
            'amount' => $amount,
            'discount_amount' => $discountAmount,
            'total_paid' => $totalPaid,
            'status' => 'Paid',
            'paid_at' => now(),
            'payer_name' => $data['payer_name'],
            'payer_email' => $data['payer_email'] ?? auth()->user()->email,
            'card_last_four' => $data['card_last_four'] ?? null,
            'transaction_note' => $data['transaction_note'] ?? null,
        ]);

        return redirect()->route('customer.payments.show', $payment)
            ->with('success', 'Payment completed successfully. Your receipt is ready.');
    }

    public function show(Payment $payment)
    {
        $this->ensurePaymentOwner($payment);
        $payment->load(['booking.vehicle', 'booking.servicePackage', 'booking.workshop', 'user']);

        return view('customer.payments.show', compact('payment'));
    }

    public function receipt(Payment $payment)
    {
        $this->ensurePaymentOwner($payment);
        $payment->load(['booking.vehicle', 'booking.servicePackage', 'booking.workshop', 'user']);

        $pdf = Pdf::loadView('pdf.receipt', [
            'payment' => $payment,
            'generatedAt' => now(),
        ])->setPaper('a4');

        return $pdf->download('receipt-' . $payment->payment_reference . '.pdf');
    }

    private function ensureOwner(Booking $booking): void
    {
        abort_if($booking->user_id !== auth()->id(), 403);
    }

    private function ensurePaymentOwner(Payment $payment): void
    {
        abort_if($payment->user_id !== auth()->id(), 403);
    }

    private function makeReference(): string
    {
        do {
            $reference = 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
        } while (Payment::where('payment_reference', $reference)->exists());

        return $reference;
    }
}
