<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServicePackageController as AdminServicePackageController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\SubscriptionPlanController as AdminSubscriptionPlanController;
use App\Http\Controllers\Admin\VehicleController as AdminVehicleController;
use App\Http\Controllers\Admin\WorkshopController as AdminWorkshopController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\PackageController as CustomerPackageController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\SubscriptionController as CustomerSubscriptionController;
use App\Http\Controllers\Customer\VehicleController as CustomerVehicleController;
use App\Http\Controllers\Customer\WorkshopController as CustomerWorkshopController;
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

        Route::get('/workshops', [CustomerWorkshopController::class, 'index'])->name('workshops.index');
        Route::get('/payments', [CustomerPaymentController::class, 'index'])->name('payments.index');
        Route::get('/bookings/{booking}/payments/create', [CustomerPaymentController::class, 'create'])->name('payments.create');
        Route::post('/bookings/{booking}/payments', [CustomerPaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [CustomerPaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{payment}/receipt', [CustomerPaymentController::class, 'receipt'])->name('payments.receipt');

        Route::get('/subscriptions', [CustomerSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscriptions/{subscriptionPlan}', [CustomerSubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::delete('/subscriptions/{userSubscription}', [CustomerSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('customers', AdminCustomerController::class)->only(['index', 'destroy']);
        Route::resource('vehicles', AdminVehicleController::class)->only(['index']);
        Route::resource('workshops', AdminWorkshopController::class)->except(['show']);
        Route::resource('service-packages', AdminServicePackageController::class)
            ->parameters(['service-packages' => 'servicePackage'])
            ->except(['show']);
        Route::resource('subscription-plans', AdminSubscriptionPlanController::class)
            ->parameters(['subscription-plans' => 'subscriptionPlan'])
            ->except(['show']);
        Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::resource('bookings', AdminBookingController::class)->only(['index', 'show']);
        Route::patch('/bookings/{booking}/approve', [AdminBookingController::class, 'approve'])->name('bookings.approve');
        Route::patch('/bookings/{booking}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');
        Route::patch('/bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments-sales/pdf', [AdminPaymentController::class, 'exportPdf'])->name('payments.export.pdf');
        Route::get('/reports/bookings/pdf', [ReportController::class, 'exportBookingsPdf'])->name('reports.bookings.pdf');
    });
});
