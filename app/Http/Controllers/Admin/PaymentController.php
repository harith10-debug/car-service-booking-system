<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = $this->filteredPaymentQuery($request);

        $payments = (clone $baseQuery)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $paidQuery = (clone $baseQuery)->where('status', 'Paid');

        return view('admin.payments.index', [
            'payments' => $payments,
            'methods' => Payment::METHODS,
            'statuses' => Payment::STATUSES,
            'totalSales' => (clone $paidQuery)->sum('total_paid'),
            'paidPayments' => (clone $paidQuery)->count(),
            'averagePayment' => (clone $paidQuery)->avg('total_paid') ?? 0,
            'methodTotals' => Payment::selectRaw('method, SUM(total_paid) as total')
                ->where('status', 'Paid')
                ->groupBy('method')
                ->orderByDesc('total')
                ->get(),
        ]);
    }

    public function show(Payment $payment)
    {
        $payment->load(['booking.vehicle', 'booking.servicePackage', 'booking.workshop', 'user']);

        return view('admin.payments.show', compact('payment'));
    }

    public function exportPdf(Request $request)
    {
        $payments = $this->filteredPaymentQuery($request)
            ->latest()
            ->get();

        $pdf = Pdf::loadView('pdf.payments', [
            'payments' => $payments,
            'filters' => $request->only(['customer', 'reference', 'method', 'status', 'date_from', 'date_to']),
            'totalSales' => $payments->where('status', 'Paid')->sum('total_paid'),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('payment-sales-report-' . now()->format('Ymd-His') . '.pdf');
    }

    public static function filteredPaymentQuery(Request $request)
    {
        return Payment::with(['booking.vehicle', 'booking.servicePackage', 'booking.workshop', 'user'])
            ->when($request->filled('customer'), function ($query) use ($request) {
                $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->customer . '%'));
            })
            ->when($request->filled('reference'), fn($query) => $query->where('payment_reference', 'like', '%' . $request->reference . '%'))
            ->when($request->filled('method'), fn($query) => $query->where('method', $request->method))
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->when($request->filled('date_from'), fn($query) => $query->whereDate('paid_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($query) => $query->whereDate('paid_at', '<=', $request->date_to));
    }
}
