<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\ServicePackage;
use App\Models\Workshop;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = auth()->user()
            ->bookings()
            ->with(['vehicle', 'servicePackage', 'workshop', 'payment'])
            ->latest()
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $vehicles = auth()->user()->vehicles()->orderBy('plate_number')->get();
        $packages = ServicePackage::where('status', 'Active')->orderBy('package_name')->get();
        $workshops = Workshop::where('status', 'Active')->orderBy('city')->orderBy('name')->get();
        $activeSubscription = auth()->user()->activeSubscription()->with('plan')->first();

        return view('customer.bookings.create', compact('vehicles', 'packages', 'workshops', 'activeSubscription'));
    }

    public function store(StoreBookingRequest $request)
    {
        $package = ServicePackage::findOrFail($request->service_package_id);

        $booking = Booking::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'status' => 'Pending',
            'total_price' => $package->price,
        ]);

        BookingStatusLog::create([
            'booking_id' => $booking->id,
            'changed_by' => auth()->id(),
            'from_status' => null,
            'to_status' => 'Pending',
            'remarks' => 'Booking created by customer.',
        ]);

        return redirect()->route('customer.bookings.show', $booking)->with('success', 'Booking submitted successfully.');
    }

    public function show(Booking $booking)
    {
        $this->ensureOwner($booking);
        $booking->load(['vehicle', 'servicePackage', 'workshop', 'payment', 'statusLogs.changedBy']);

        return view('customer.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $this->ensureOwner($booking);
        abort_if(!$booking->canBeEditedByCustomer(), 403, 'Only pending bookings can be edited.');

        $vehicles = auth()->user()->vehicles()->orderBy('plate_number')->get();
        $packages = ServicePackage::where('status', 'Active')->orderBy('package_name')->get();
        $workshops = Workshop::where('status', 'Active')->orderBy('city')->orderBy('name')->get();
        $activeSubscription = auth()->user()->activeSubscription()->with('plan')->first();

        return view('customer.bookings.edit', compact('booking', 'vehicles', 'packages', 'workshops', 'activeSubscription'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $this->ensureOwner($booking);
        abort_if(!$booking->canBeEditedByCustomer(), 403, 'Only pending bookings can be edited.');

        $package = ServicePackage::findOrFail($request->service_package_id);

        $booking->update([
            ...$request->validated(),
            'total_price' => $package->price,
        ]);

        return redirect()->route('customer.bookings.show', $booking)->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $this->ensureOwner($booking);
        abort_if(!$booking->canBeCancelledByCustomer(), 403, 'This booking can no longer be cancelled.');

        $oldStatus = $booking->status;
        $booking->update(['status' => 'Cancelled']);

        BookingStatusLog::create([
            'booking_id' => $booking->id,
            'changed_by' => auth()->id(),
            'from_status' => $oldStatus,
            'to_status' => 'Cancelled',
            'remarks' => 'Booking cancelled by customer.',
        ]);

        return redirect()->route('customer.bookings.index')->with('success', 'Booking cancelled successfully.');
    }

    private function ensureOwner(Booking $booking): void
    {
        abort_if($booking->user_id !== auth()->id(), 403);
    }
}
