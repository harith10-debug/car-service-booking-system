<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = auth()->user()->vehicles()->latest()->paginate(10);
        return view('customer.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('customer.vehicles.create');
    }

    public function store(StoreVehicleRequest $request)
    {
        auth()->user()->vehicles()->create($request->validated());
        return redirect()->route('customer.vehicles.index')->with('success', 'Vehicle added successfully.');
    }

    public function edit(Vehicle $vehicle)
    {
        $this->ensureOwner($vehicle);
        return view('customer.vehicles.edit', compact('vehicle'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $this->ensureOwner($vehicle);
        $vehicle->update($request->validated());

        return redirect()->route('customer.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->ensureOwner($vehicle);

        if ($vehicle->bookings()->exists()) {
            return back()->with('error', 'This vehicle cannot be deleted because it has booking records.');
        }

        $vehicle->delete();
        return redirect()->route('customer.vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }

    private function ensureOwner(Vehicle $vehicle): void
    {
        abort_if($vehicle->user_id !== auth()->id(), 403);
    }
}
