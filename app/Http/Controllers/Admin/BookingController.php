<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingStatusLog;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $this->filteredBookingQuery($request)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'statuses' => Booking::STATUSES,
        ]);
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'vehicle', 'servicePackage', 'workshop', 'payment', 'statusLogs.changedBy']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function approve(Booking $booking)
    {
        return $this->changeStatus($booking, 'Approved', 'Booking approved by admin.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'admin_remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        return $this->changeStatus($booking, 'Rejected', $data['admin_remarks'] ?? 'Booking rejected by admin.');
    }

    public function complete(Booking $booking)
    {
        return $this->changeStatus($booking, 'Completed', 'Service completed by admin.');
    }

    private function changeStatus(Booking $booking, string $status, string $remarks)
    {
        $oldStatus = $booking->status;

        if ($oldStatus === $status) {
            return back()->with('error', "Booking is already {$status}.");
        }

        if ($oldStatus === 'Cancelled') {
            return back()->with('error', 'Cancelled bookings cannot be updated.');
        }

        if ($status === 'Rejected' && $booking->payment()->where('status', 'Paid')->exists()) {
            return back()->with('error', 'Paid bookings cannot be rejected.');
        }

        $booking->update([
            'status' => $status,
            'admin_remarks' => $remarks,
        ]);

        BookingStatusLog::create([
            'booking_id' => $booking->id,
            'changed_by' => auth()->id(),
            'from_status' => $oldStatus,
            'to_status' => $status,
            'remarks' => $remarks,
        ]);

        return back()->with('success', "Booking marked as {$status}.");
    }

    public static function filteredBookingQuery(Request $request)
    {
        return Booking::with(['user', 'vehicle', 'servicePackage', 'workshop', 'payment'])
            ->when($request->filled('customer'), function ($query) use ($request) {
                $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->customer . '%'));
            })
            ->when($request->filled('plate_number'), function ($query) use ($request) {
                $query->whereHas('vehicle', fn($q) => $q->where('plate_number', 'like', '%' . strtoupper($request->plate_number) . '%'));
            })
            ->when($request->filled('service_type'), function ($query) use ($request) {
                $query->whereHas('servicePackage', fn($q) => $q->where('package_name', 'like', '%' . $request->service_type . '%'));
            })
            ->when($request->filled('preferred_date'), fn($query) => $query->whereDate('preferred_date', $request->preferred_date))
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->when($request->filled('payment_status'), function ($query) use ($request) {
                if ($request->payment_status === 'Paid') {
                    $query->whereHas('payment', fn($q) => $q->where('status', 'Paid'));
                } elseif ($request->payment_status === 'Unpaid') {
                    $query->whereDoesntHave('payment');
                }
            })
            ->when($request->filled('workshop'), function ($query) use ($request) {
                $query->whereHas('workshop', fn($q) => $q->where('name', 'like', '%' . $request->workshop . '%'));
            });
    }
}
