<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServicePackageController as AdminServicePackageController;
use App\Http\Controllers\Admin\VehicleController as AdminVehicleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\PackageController as CustomerPackageController;
use App\Http\Controllers\Customer\VehicleController as CustomerVehicleController;
use App\Http\Controllers\DashboardController;
use App\Models\ServicePackage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    $landingPackages = collect();

    try {
        $landingPackages = ServicePackage::where('status', 'Active')
            ->orderBy('price')
            ->take(6)
            ->get();
    } catch (Throwable $exception) {
        // Keep the public landing page available even before database migration.
        $landingPackages = collect();
    }

    return view('welcome', compact('landingPackages'));
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');
        Route::resource('vehicles', CustomerVehicleController::class)->except(['show']);
        Route::get('/packages', [CustomerPackageController::class, 'index'])->name('packages.index');
        Route::resource('bookings', CustomerBookingController::class);
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('customers', AdminCustomerController::class)->only(['index', 'destroy']);
        Route::resource('vehicles', AdminVehicleController::class)->only(['index']);
        Route::resource('service-packages', AdminServicePackageController::class)
            ->parameters(['service-packages' => 'servicePackage'])
            ->except(['show']);
        Route::resource('bookings', AdminBookingController::class)->only(['index', 'show']);
        Route::patch('/bookings/{booking}/approve', [AdminBookingController::class, 'approve'])->name('bookings.approve');
        Route::patch('/bookings/{booking}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');
        Route::patch('/bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
        Route::get('/reports/bookings/pdf', [ReportController::class, 'exportBookingsPdf'])->name('reports.bookings.pdf');
    });
});
