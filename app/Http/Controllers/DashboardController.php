<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\ServicePackage;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Vehicle;
use App\Models\Workshop;

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
            'approvedBookings' => Booking::where('status', 'Approved')->count(),
            'completedBookings' => Booking::where('status', 'Completed')->count(),
            'paidBookings' => Booking::whereHas('payment', fn($query) => $query->where('status', 'Paid'))->count(),
            'totalSales' => Payment::where('status', 'Paid')->sum('total_paid'),
            'todaySales' => Payment::where('status', 'Paid')->whereDate('paid_at', today())->sum('total_paid'),
            'activeSubscriptions' => UserSubscription::where('status', 'Active')->where('ends_at', '>=', now())->count(),
            'subscriptionPlanCount' => SubscriptionPlan::where('status', 'Active')->count(),
            'workshopCount' => Workshop::where('status', 'Active')->count(),
            'latestBookings' => Booking::with(['user', 'vehicle', 'servicePackage', 'workshop', 'payment'])->latest()->take(5)->get(),
            'pendingQueue' => Booking::with(['user', 'vehicle', 'servicePackage', 'workshop'])->where('status', 'Pending')->oldest('preferred_date')->take(5)->get(),
            'recentPayments' => Payment::with(['booking.vehicle', 'booking.servicePackage', 'user'])->where('status', 'Paid')->latest()->take(5)->get(),
        ]);
    }

    public function customer()
    {
        $user = auth()->user();

        return view('dashboards.customer', [
            'vehicleCount' => $user->vehicles()->count(),
            'bookingCount' => $user->bookings()->count(),
            'pendingBookings' => $user->bookings()->where('status', 'Pending')->count(),
            'unpaidApprovedBookings' => $user->bookings()->where('status', 'Approved')->whereDoesntHave('payment')->count(),
            'totalPaid' => $user->payments()->where('status', 'Paid')->sum('total_paid'),
            'latestBookings' => $user->bookings()->with(['vehicle', 'servicePackage', 'workshop', 'payment'])->latest()->take(5)->get(),
            'activePackages' => ServicePackage::where('status', 'Active')->latest()->take(3)->get(),
            'activeSubscription' => $user->activeSubscription()->with('plan')->first(),
            'nearestWorkshops' => Workshop::where('status', 'Active')->orderBy('city')->take(3)->get(),
        ]);
    }
}
