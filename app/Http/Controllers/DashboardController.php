<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\Vehicle;

class DashboardController extends Controller
{
    public function index()
    {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('customer.dashboard');
    }

    public function admin()
    {
        return view('dashboards.admin', [
            'totalCustomers' => User::where('role', 'customer')->count(),
            'totalVehicles' => Vehicle::count(),
            'totalPackages' => ServicePackage::count(),
            'totalBookings' => Booking::count(),
            'pendingBookings' => Booking::where('status', 'Pending')->count(),
            'completedBookings' => Booking::where('status', 'Completed')->count(),
            'latestBookings' => Booking::with(['user', 'vehicle', 'servicePackage'])->latest()->take(5)->get(),
        ]);
    }

    public function customer()
    {
        $user = auth()->user();

        return view('dashboards.customer', [
            'vehicleCount' => $user->vehicles()->count(),
            'bookingCount' => $user->bookings()->count(),
            'pendingBookings' => $user->bookings()->where('status', 'Pending')->count(),
            'latestBookings' => $user->bookings()->with(['vehicle', 'servicePackage'])->latest()->take(5)->get(),
            'activePackages' => ServicePackage::where('status', 'Active')->latest()->take(3)->get(),
        ]);
    }
}
