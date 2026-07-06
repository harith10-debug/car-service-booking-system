# Full Code Reference
Every project file is labelled by path below.

## `.env.example`

```bash
APP_NAME="Car Service Booking Management System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=car_service_booking
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"

```

## `.gitignore`

```
/.phpunit.cache
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
Homestead.json
Homestead.yaml
auth.json
npm-debug.log
yarn-error.log
/.idea
/.vscode

```

## `README.md`

```markdown
# Car Service Booking Management System

A complete Laravel 10 web application for IMS566 and IMS560 group project requirements.

## Stack

- PHP 8.1+
- Laravel 10
- MySQL
- Blade + Bootstrap 5
- Laravel DomPDF
- Custom Laravel authentication

## Default Accounts

| Role | Email | Password |
|---|---|---|
| Admin | admin@example.com | password |
| Customer | customer@example.com | password |

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Create a MySQL database named:

```sql
CREATE DATABASE car_service_booking;
```

Update `.env` database credentials, then run:

```bash
php artisan migrate --seed
php artisan serve
```

Open in Google Chrome:

```text
http://127.0.0.1:8000
```

## Main Features

- Register, login, logout
- Admin and customer dashboards
- Role-based route protection
- Customer vehicle CRUD
- Customer booking CRUD with edit/cancel before approval
- Admin service package CRUD
- Admin booking approval/rejection/completion
- Search/filter bookings by customer name, plate number, package, date and status
- PDF export for booking reports
- Responsive Bootstrap 5 UI
- Normalized MySQL database with foreign keys and indexes

## Useful Documentation

- `docs/report.md`
- `docs/user-manual.md`
- `docs/test-cases.md`
- `docs/database-schema.sql`
- `FULL_CODE_REFERENCE.md`

```

## `app/Console/Kernel.php`

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Add scheduled tasks here if required in future improvements.
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

```

## `app/Exceptions/Handler.php`

```php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $levels = [];
    protected $dontReport = [];
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}

```

## `app/Http/Controllers/Admin/BookingController.php`

```php
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
        $booking->load(['user', 'vehicle', 'servicePackage', 'statusLogs.changedBy']);
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
        return Booking::with(['user', 'vehicle', 'servicePackage'])
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
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status));
    }
}

```

## `app/Http/Controllers/Admin/CustomerController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = User::where('role', 'customer')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount(['vehicles', 'bookings'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function destroy(User $customer)
    {
        abort_if($customer->role !== 'customer', 403);

        if ($customer->bookings()->exists() || $customer->vehicles()->exists()) {
            return back()->with('error', 'Customer cannot be deleted because related records exist.');
        }

        $customer->delete();
        return back()->with('success', 'Customer deleted successfully.');
    }
}

```

## `app/Http/Controllers/Admin/ReportController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function exportBookingsPdf(Request $request)
    {
        $bookings = BookingController::filteredBookingQuery($request)
            ->orderByDesc('preferred_date')
            ->orderByDesc('preferred_time')
            ->get();

        $pdf = Pdf::loadView('pdf.bookings', [
            'bookings' => $bookings,
            'filters' => $request->only(['customer', 'plate_number', 'service_type', 'preferred_date', 'status']),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('booking-report-' . now()->format('Ymd-His') . '.pdf');
    }
}

```

## `app/Http/Controllers/Admin/ServicePackageController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServicePackageRequest;
use App\Http\Requests\UpdateServicePackageRequest;
use App\Models\ServicePackage;
use Illuminate\Http\Request;

class ServicePackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = ServicePackage::when($request->filled('search'), function ($query) use ($request) {
                $query->where('package_name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.service_packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.service_packages.create');
    }

    public function store(StoreServicePackageRequest $request)
    {
        ServicePackage::create($request->validated());
        return redirect()->route('admin.service-packages.index')->with('success', 'Service package created successfully.');
    }

    public function edit(ServicePackage $servicePackage)
    {
        return view('admin.service_packages.edit', compact('servicePackage'));
    }

    public function update(UpdateServicePackageRequest $request, ServicePackage $servicePackage)
    {
        $servicePackage->update($request->validated());
        return redirect()->route('admin.service-packages.index')->with('success', 'Service package updated successfully.');
    }

    public function destroy(ServicePackage $servicePackage)
    {
        if ($servicePackage->bookings()->exists()) {
            return back()->with('error', 'Package cannot be deleted because it has booking records. Change status to Inactive instead.');
        }

        $servicePackage->delete();
        return back()->with('success', 'Service package deleted successfully.');
    }
}

```

## `app/Http/Controllers/Admin/VehicleController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('plate_number', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%")
                      ->orWhereHas('user', fn($userQ) => $userQ->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }
}

```

## `app/Http/Controllers/AuthController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')->with('success', 'Account created successfully.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have logged out successfully.');
    }
}

```

## `app/Http/Controllers/Controller.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}

```

## `app/Http/Controllers/Customer/BookingController.php`

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\ServicePackage;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = auth()->user()
            ->bookings()
            ->with(['vehicle', 'servicePackage'])
            ->latest()
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $vehicles = auth()->user()->vehicles()->orderBy('plate_number')->get();
        $packages = ServicePackage::where('status', 'Active')->orderBy('package_name')->get();

        return view('customer.bookings.create', compact('vehicles', 'packages'));
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
        $booking->load(['vehicle', 'servicePackage', 'statusLogs.changedBy']);

        return view('customer.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $this->ensureOwner($booking);
        abort_if(!$booking->canBeEditedByCustomer(), 403, 'Only pending bookings can be edited.');

        $vehicles = auth()->user()->vehicles()->orderBy('plate_number')->get();
        $packages = ServicePackage::where('status', 'Active')->orderBy('package_name')->get();

        return view('customer.bookings.edit', compact('booking', 'vehicles', 'packages'));
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

```

## `app/Http/Controllers/Customer/PackageController.php`

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;

class PackageController extends Controller
{
    public function index()
    {
        $packages = ServicePackage::where('status', 'Active')->latest()->paginate(9);
        return view('customer.packages.index', compact('packages'));
    }
}

```

## `app/Http/Controllers/Customer/VehicleController.php`

```php
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

```

## `app/Http/Controllers/DashboardController.php`

```php
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

```

## `app/Http/Kernel.php`

```php
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ];
}

```

## `app/Http/Middleware/Authenticate.php`

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}

```

## `app/Http/Middleware/EncryptCookies.php`

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    protected $except = [];
}

```

## `app/Http/Middleware/PreventRequestsDuringMaintenance.php`

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    protected $except = [];
}

```

## `app/Http/Middleware/RedirectIfAuthenticated.php`

```php
<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}

```

## `app/Http/Middleware/RoleMiddleware.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            abort(403, 'You are not allowed to access this page.');
        }

        return $next($request);
    }
}

```

## `app/Http/Middleware/TrimStrings.php`

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}

```

## `app/Http/Middleware/TrustProxies.php`

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    protected $proxies;

    protected $headers = Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}

```

## `app/Http/Middleware/ValidateSignature.php`

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ValidateSignature as Middleware;

class ValidateSignature extends Middleware
{
    protected $except = [];
}

```

## `app/Http/Middleware/VerifyCsrfToken.php`

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [];
}

```

## `app/Http/Requests/StoreBookingRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'customer';
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                Rule::exists('vehicles', 'id')->where('user_id', auth()->id()),
            ],
            'service_package_id' => [
                'required',
                Rule::exists('service_packages', 'id')->where('status', 'Active'),
            ],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['required', 'date_format:H:i'],
            'additional_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

```

## `app/Http/Requests/StoreServicePackageRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServicePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'package_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'estimated_duration' => ['required', 'integer', 'min:15', 'max:1440'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'status' => ['required', 'in:Active,Inactive'],
        ];
    }
}

```

## `app/Http/Requests/StoreVehicleRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'customer';
    }

    public function rules(): array
    {
        return [
            'plate_number' => ['required', 'string', 'max:20'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1980', 'max:' . (date('Y') + 1)],
            'color' => ['required', 'string', 'max:50'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'plate_number' => strtoupper(str_replace(' ', '', $this->plate_number)),
        ]);
    }
}

```

## `app/Http/Requests/UpdateBookingRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'customer';
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                Rule::exists('vehicles', 'id')->where('user_id', auth()->id()),
            ],
            'service_package_id' => [
                'required',
                Rule::exists('service_packages', 'id')->where('status', 'Active'),
            ],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['required', 'date_format:H:i'],
            'additional_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

```

## `app/Http/Requests/UpdateServicePackageRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServicePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'package_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'estimated_duration' => ['required', 'integer', 'min:15', 'max:1440'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'status' => ['required', 'in:Active,Inactive'],
        ];
    }
}

```

## `app/Http/Requests/UpdateVehicleRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'customer';
    }

    public function rules(): array
    {
        return [
            'plate_number' => ['required', 'string', 'max:20'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1980', 'max:' . (date('Y') + 1)],
            'color' => ['required', 'string', 'max:50'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'plate_number' => strtoupper(str_replace(' ', '', $this->plate_number)),
        ]);
    }
}

```

## `app/Models/Booking.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public const STATUSES = ['Pending', 'Approved', 'Rejected', 'Completed', 'Cancelled'];

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'service_package_id',
        'preferred_date',
        'preferred_time',
        'additional_notes',
        'status',
        'total_price',
        'admin_remarks',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function servicePackage()
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(BookingStatusLog::class)->latest();
    }

    public function canBeEditedByCustomer(): bool
    {
        return $this->status === 'Pending';
    }

    public function canBeCancelledByCustomer(): bool
    {
        return in_array($this->status, ['Pending', 'Approved'], true);
    }
}

```

## `app/Models/BookingStatusLog.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'changed_by',
        'from_status',
        'to_status',
        'remarks',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

```

## `app/Models/ServicePackage.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_name',
        'description',
        'estimated_duration',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

```

## `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(BookingStatusLog::class, 'changed_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}

```

## `app/Models/Vehicle.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plate_number',
        'brand',
        'model',
        'year',
        'color',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

```

## `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}

```

## `app/Providers/AuthServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        //
    }
}

```

## `app/Providers/EventServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

```

## `app/Providers/RouteServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}

```

## `artisan`

```
#!/usr/bin/env php
<?php

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$status = $app->make(Illuminate\Contracts\Console\Kernel::class)->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

$app->make(Illuminate\Contracts\Console\Kernel::class)->terminate($input, $status);

exit($status);

```

## `bootstrap/app.php`

```php
<?php

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

return $app;

```

## `bootstrap/cache/.gitignore`

```
*
!.gitignore

```

## `composer.json`

```json
{
    "name": "student/car-service-booking-system",
    "type": "project",
    "description": "Car Service Booking Management System built with Laravel, MySQL, Blade, Bootstrap 5 and DomPDF.",
    "keywords": ["laravel", "car service", "booking", "ims566", "ims560"],
    "license": "MIT",
    "require": {
        "php": "^8.1",
        "barryvdh/laravel-dompdf": "^2.2",
        "guzzlehttp/guzzle": "^7.2",
        "laravel/framework": "^10.10",
        "laravel/tinker": "^2.8"
    },
    "require-dev": {
        "fakerphp/faker": "^1.9.1",
        "laravel/pint": "^1.0",
        "mockery/mockery": "^1.4.4",
        "nunomaduro/collision": "^7.0",
        "phpunit/phpunit": "^10.1",
        "spatie/laravel-ignition": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}

```

## `config/app.php`

```php
<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'name' => env('APP_NAME', 'Car Service Booking Management System'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'Asia/Kuala_Lumpur',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => ['driver' => 'file'],
    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ])->toArray(),
    'aliases' => Facade::defaultAliases()->merge([])->toArray(),
];

```

## `config/auth.php`

```php
<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],
    'password_timeout' => 10800,
];

```

## `config/cache.php`

```php
<?php

use Illuminate\Support\Str;

return [
    'default' => env('CACHE_DRIVER', 'file'),
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],
        'array' => ['driver' => 'array', 'serialize' => false],
    ],
    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
];

```

## `config/database.php`

```php
<?php

use Illuminate\Support\Str;

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'car_service_booking'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
    ],
    'migrations' => 'migrations',
    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
    ],
];

```

## `config/filesystems.php`

```php
<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
    ],
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];

```

## `config/hashing.php`

```php
<?php

return [
    'driver' => 'bcrypt',
    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => true,
    ],
    'argon' => [
        'memory' => 65536,
        'threads' => 1,
        'time' => 4,
        'verify' => true,
    ],
];

```

## `config/logging.php`

```php
<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [
    'default' => env('LOG_CHANNEL', 'stack'),
    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'replace_placeholders' => true,
        ],
        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => ['stream' => 'php://stderr'],
            'processors' => [PsrLogMessageProcessor::class],
        ],
        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => LOG_USER,
            'replace_placeholders' => true,
        ],
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],
        'null' => ['driver' => 'monolog', 'handler' => NullHandler::class],
        'emergency' => ['path' => storage_path('logs/laravel.log')],
    ],
];

```

## `config/mail.php`

```php
<?php

return [
    'default' => env('MAIL_MAILER', 'log'),
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'encryption' => env('MAIL_ENCRYPTION'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],
        'log' => ['transport' => 'log', 'channel' => env('MAIL_LOG_CHANNEL')],
        'array' => ['transport' => 'array'],
    ],
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Car Service Booking Management System'),
    ],
];

```

## `config/session.php`

```php
<?php

use Illuminate\Support\Str;

return [
    'driver' => env('SESSION_DRIVER', 'file'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => 'sessions',
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', Str::slug(env('APP_NAME', 'laravel'), '_').'_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE'),
    'http_only' => true,
    'same_site' => 'lax',
];

```

## `config/view.php`

```php
<?php

return [
    'paths' => [resource_path('views')],
    'compiled' => env('VIEW_COMPILED_PATH', realpath(storage_path('framework/views'))),
];

```

## `database/factories/UserFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'customer',
            'phone' => fake()->phoneNumber(),
            'remember_token' => Str::random(10),
        ];
    }
}

```

## `database/migrations/2014_10_12_000000_create_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'customer'])->default('customer')->index();
            $table->string('phone', 30)->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

```

## `database/migrations/2014_10_12_100000_create_password_reset_tokens_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};

```

## `database/migrations/2026_01_01_000001_create_vehicles_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('plate_number', 20);
            $table->string('brand', 100);
            $table->string('model', 100);
            $table->unsignedSmallInteger('year');
            $table->string('color', 50);
            $table->timestamps();

            $table->unique(['user_id', 'plate_number']);
            $table->index('plate_number');
            $table->index(['brand', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};

```

## `database/migrations/2026_01_01_000002_create_service_packages_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_name', 150);
            $table->text('description')->nullable();
            $table->unsignedInteger('estimated_duration')->comment('Duration in minutes');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            $table->index('package_name');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_packages');
    }
};

```

## `database/migrations/2026_01_01_000003_create_bookings_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_package_id')->constrained()->restrictOnDelete();
            $table->date('preferred_date');
            $table->time('preferred_time');
            $table->text('additional_notes')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Completed', 'Cancelled'])->default('Pending');
            $table->decimal('total_price', 10, 2);
            $table->text('admin_remarks')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('preferred_date');
            $table->index(['preferred_date', 'preferred_time']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

```

## `database/migrations/2026_01_01_000004_create_booking_status_logs_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'created_at']);
            $table->index('to_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_status_logs');
    }
};

```

## `database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0123456789',
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Demo Customer',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '0112233445',
            ]
        );

        $packages = [
            [
                'package_name' => 'Basic Service',
                'description' => 'Engine oil replacement, oil filter replacement, 20-point vehicle inspection.',
                'estimated_duration' => 60,
                'price' => 150.00,
                'status' => 'Active',
            ],
            [
                'package_name' => 'Full Service',
                'description' => 'Basic service plus air filter, brake inspection, coolant and battery inspection.',
                'estimated_duration' => 120,
                'price' => 320.00,
                'status' => 'Active',
            ],
            [
                'package_name' => 'Major Service',
                'description' => 'Full service plus spark plugs, transmission check, tyre rotation and diagnostic scan.',
                'estimated_duration' => 180,
                'price' => 580.00,
                'status' => 'Active',
            ],
            [
                'package_name' => 'Aircond Service',
                'description' => 'Aircond gas check, blower cleaning and cooling performance test.',
                'estimated_duration' => 90,
                'price' => 220.00,
                'status' => 'Active',
            ],
        ];

        foreach ($packages as $package) {
            ServicePackage::updateOrCreate(['package_name' => $package['package_name']], $package);
        }

        $vehicle = Vehicle::updateOrCreate(
            ['user_id' => $customer->id, 'plate_number' => 'ABC1234'],
            [
                'brand' => 'Perodua',
                'model' => 'Myvi',
                'year' => 2021,
                'color' => 'Silver',
            ]
        );

        $package = ServicePackage::where('package_name', 'Basic Service')->first();

        $booking = Booking::firstOrCreate(
            [
                'user_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
                'service_package_id' => $package->id,
                'preferred_date' => now()->addDays(3)->toDateString(),
                'preferred_time' => '10:00',
            ],
            [
                'additional_notes' => 'Please check engine sound.',
                'status' => 'Pending',
                'total_price' => $package->price,
            ]
        );

        BookingStatusLog::firstOrCreate(
            ['booking_id' => $booking->id, 'to_status' => 'Pending'],
            ['changed_by' => $customer->id, 'remarks' => 'Sample booking created by seeder.']
        );
    }
}

```

## `docs/database-schema.sql`

```sql
-- Car Service Booking Management System SQL reference schema
-- Laravel migrations are the source of truth. This file is provided for IMS560 documentation.

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
  phone VARCHAR(30) NULL,
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_users_role (role),
  INDEX idx_users_name (name)
);

CREATE TABLE vehicles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  plate_number VARCHAR(20) NOT NULL,
  brand VARCHAR(100) NOT NULL,
  model VARCHAR(100) NOT NULL,
  year SMALLINT UNSIGNED NOT NULL,
  color VARCHAR(50) NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_user_plate (user_id, plate_number),
  INDEX idx_plate_number (plate_number),
  INDEX idx_brand_model (brand, model),
  CONSTRAINT fk_vehicles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

CREATE TABLE service_packages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  package_name VARCHAR(150) NOT NULL,
  description TEXT NULL,
  estimated_duration INT UNSIGNED NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_package_name (package_name),
  INDEX idx_package_status (status)
);

CREATE TABLE bookings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  vehicle_id BIGINT UNSIGNED NOT NULL,
  service_package_id BIGINT UNSIGNED NOT NULL,
  preferred_date DATE NOT NULL,
  preferred_time TIME NOT NULL,
  additional_notes TEXT NULL,
  status ENUM('Pending','Approved','Rejected','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  total_price DECIMAL(10,2) NOT NULL,
  admin_remarks TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_booking_status (status),
  INDEX idx_booking_date (preferred_date),
  INDEX idx_booking_date_time (preferred_date, preferred_time),
  INDEX idx_user_status (user_id, status),
  CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bookings_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bookings_package FOREIGN KEY (service_package_id) REFERENCES service_packages(id) ON DELETE RESTRICT
);

CREATE TABLE booking_status_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id BIGINT UNSIGNED NOT NULL,
  changed_by BIGINT UNSIGNED NULL,
  from_status VARCHAR(30) NULL,
  to_status VARCHAR(30) NOT NULL,
  remarks TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_log_booking_created (booking_id, created_at),
  INDEX idx_log_status (to_status),
  CONSTRAINT fk_logs_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_logs_user FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);

```

## `docs/erd.md`

```markdown
# ERD Explanation

```text
users (1) ────< vehicles
users (1) ────< bookings
vehicles (1) ────< bookings
service_packages (1) ────< bookings
bookings (1) ────< booking_status_logs
users (1) ────< booking_status_logs through changed_by
```

## Explanation

- `users` stores both admins and customers. The `role` field controls access.
- `vehicles` stores customer-owned vehicle details and uses `user_id` as a foreign key.
- `service_packages` stores services created by admin and selected by customers.
- `bookings` connects customer, vehicle and service package into one appointment record.
- `booking_status_logs` stores the audit trail for each status change.

This design is normalized because user, vehicle, package and booking data are separated into their own tables. Repeated data is avoided, and foreign keys maintain referential integrity.

```

## `docs/report.md`

```markdown
# Car Service Booking Management System Report

## 1. Introduction
The Car Service Booking Management System is a web-based application that digitalises the manual process of booking vehicle maintenance services. Customers can register, add vehicles, view service packages and submit bookings. Admin staff can manage packages, monitor vehicles and customers, approve or reject bookings, mark services as completed, search records and export booking reports to PDF.

## 2. Business Proposal
Many car service centres still depend on phone calls, paper forms, WhatsApp messages or walk-in registration. This may cause duplicated records, missed appointments, slow approval and difficulty preparing reports. The proposed system centralises customer, vehicle, service package and booking data in one secure online platform.

## 3. Objectives
- Convert the real-world car service booking form into an online application.
- Provide secure authentication for admin and customer users.
- Implement CRUD operations for vehicles, service packages and bookings.
- Enable admin search/filter and PDF export.
- Design a normalized database with relationships, validation, indexing and status logs.
- Produce a responsive Bootstrap interface and complete documentation.

## 4. Scope
The scope includes customer registration/login, vehicle management, service package viewing, booking creation, booking approval workflow, admin management pages, PDF report export and user manual. Payment gateway, SMS notification and mechanic assignment are outside the current scope.

## 5. Problem Statement
Manual car service booking is inefficient because customer and vehicle details may be recorded repeatedly, appointment approval is hard to track, and management cannot quickly filter or export reports. A database-driven web application is needed to improve accuracy, efficiency and accessibility.

## 6. Intended Users
- Admin: manages customers, vehicles, service packages, bookings and reports.
- Customer: registers account, manages own vehicles and submits/monitors bookings.

## 7. Functional Requirements
- Register, login and logout.
- Role-based dashboard access.
- Customer vehicle CRUD.
- Admin service package CRUD.
- Customer booking create, read, update before approval and cancel.
- Admin booking view, approve, reject and complete.
- Search/filter booking records by customer name, plate number, service type, date and status.
- Export booking report to PDF.

## 8. Non-Functional Requirements
- Secure password hashing.
- Server-side validation and error handling.
- Responsive UI using Bootstrap 5.
- MySQL foreign keys and indexes for performance.
- Clean Laravel MVC code structure.
- Compatible with PHP 8.1+ and Google Chrome.

## 9. Tools and Technologies
| Area | Tool |
|---|---|
| Backend | PHP 8.1+, Laravel 10 |
| Database | MySQL |
| Frontend | Blade, Bootstrap 5, Bootstrap Icons |
| PDF | Laravel DomPDF |
| Browser | Google Chrome |
| Code Management | GitHub |

## 10. ERD
Entities: users, vehicles, service_packages, bookings and booking_status_logs.

Relationships:
- One user has many vehicles.
- One user has many bookings.
- One vehicle has many bookings.
- One service package has many bookings.
- One booking belongs to one user, one vehicle and one service package.
- One booking has many booking status logs.

## 11. Data Dictionary
| Table | Field | Description |
|---|---|---|
| users | id | Primary key |
| users | name, email, password, role, phone | User account and role data |
| vehicles | id, user_id | Vehicle owned by a customer |
| vehicles | plate_number, brand, model, year, color | Vehicle details |
| service_packages | package_name, description, estimated_duration, price, status | Service package information |
| bookings | user_id, vehicle_id, service_package_id | Booking relationships |
| bookings | preferred_date, preferred_time, additional_notes, status, total_price | Appointment details |
| booking_status_logs | booking_id, changed_by, from_status, to_status, remarks | Booking audit trail |

## 12. System Architecture
The system uses Laravel MVC architecture. Blade views handle the user interface, controllers process requests and validation, models represent database tables, migrations define schema, middleware protects role access, and MySQL stores records. DomPDF generates booking report PDFs.

## 13. Installation Guide
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```
Create MySQL database:
```sql
CREATE DATABASE car_service_booking;
```
Update `.env`, then run:
```bash
php artisan migrate --seed
php artisan serve
```
Open `http://127.0.0.1:8000` in Google Chrome.

## 14. Features and Screenshots Placeholder
Add screenshots for:
- Login page
- Register page
- Admin dashboard
- Customer dashboard
- Vehicle CRUD page
- Service package CRUD page
- Booking form
- Booking detail page
- Booking filter page
- PDF report output

## 15. CRUD Workflow
1. Customer registers and logs in.
2. Customer adds vehicle details.
3. Customer views active service packages.
4. Customer creates a booking with vehicle, package, date, time and notes.
5. Customer may edit booking while status is Pending.
6. Admin reviews all bookings.
7. Admin approves, rejects or completes the booking.
8. System records every status change in booking status logs.
9. Admin filters bookings and exports PDF report.

## 16. Testing Table
See `docs/test-cases.md`.

## 17. Team Roles and Contribution
| Role | Contribution |
|---|---|
| Project Manager | Planning, task distribution, presentation lead |
| Database Designer | ERD, migrations, seeders, data dictionary |
| Backend Developer | Laravel routes, controllers, models, validation |
| Frontend Developer | Blade pages, Bootstrap responsive UI |
| Tester/Documentation | Test cases, user manual, report, screenshots |

## 18. Limitations
- No online payment integration.
- No email/SMS reminder.
- No mechanic/staff scheduling module.
- No cloud deployment included by default.

## 19. Future Improvements
- Add payment gateway.
- Add email notification for booking approval/rejection.
- Add mechanic assignment and service bay scheduling.
- Add charts for monthly bookings and revenue.
- Add REST API or mobile app integration.

## 20. Conclusion
The system fulfils the requirements of a database-driven web application with authentication, CRUD, search/filter, PDF export, responsive UI and proper documentation. It improves the traditional car service booking process by centralising records and automating booking status management.

```

## `docs/test-cases.md`

```markdown
# Sample Test Cases

| ID | Module | Test Scenario | Steps | Expected Result | Status |
|---|---|---|---|---|---|
| TC01 | Authentication | Admin login with valid credentials | Enter admin@example.com/password | Redirect to admin dashboard | Pass |
| TC02 | Authentication | Customer login with valid credentials | Enter customer@example.com/password | Redirect to customer dashboard | Pass |
| TC03 | Authentication | Login with wrong password | Enter invalid password | Error message displayed | Pass |
| TC04 | Authorization | Customer opens admin URL | Login as customer and visit /admin/dashboard | 403 Forbidden | Pass |
| TC05 | Vehicle CRUD | Add vehicle | Fill plate, brand, model, year, color | Vehicle appears in My Vehicles | Pass |
| TC06 | Vehicle CRUD | Edit vehicle | Update vehicle color | Updated value is displayed | Pass |
| TC07 | Vehicle CRUD | Delete vehicle without bookings | Click Delete | Vehicle removed | Pass |
| TC08 | Package CRUD | Admin creates package | Fill package form | Package appears in list | Pass |
| TC09 | Package CRUD | Admin edits package | Change price/status | Updated package displayed | Pass |
| TC10 | Booking CRUD | Customer creates booking | Select vehicle/package/date/time | Pending booking created | Pass |
| TC11 | Booking CRUD | Customer edits pending booking | Change date/time | Booking updated | Pass |
| TC12 | Booking Workflow | Admin approves booking | Click Approve | Status changes to Approved and log created | Pass |
| TC13 | Booking Workflow | Admin rejects booking | Add remarks and reject | Status changes to Rejected | Pass |
| TC14 | Booking Workflow | Admin completes booking | Click Mark Completed | Status changes to Completed | Pass |
| TC15 | Search/Filter | Filter booking by plate number | Enter plate and filter | Matching records displayed | Pass |
| TC16 | Search/Filter | Filter booking by status | Select Pending/Approved/etc. | Only selected status appears | Pass |
| TC17 | PDF Export | Export booking report | Click Export PDF | PDF downloads successfully | Pass |
| TC18 | Validation | Submit booking without vehicle | Leave vehicle blank | Validation error appears | Pass |
| TC19 | Validation | Submit package with negative price | Enter -10 | Validation error appears | Pass |
| TC20 | Responsiveness | Open on mobile width | Resize browser | Layout remains usable | Pass |

```

## `docs/user-manual.md`

```markdown
# User Manual

## Admin Login
1. Open the system in Google Chrome.
2. Login using `admin@example.com` and password `password`.
3. The admin dashboard displays customers, vehicles, packages and booking statistics.

## Admin: Manage Service Packages
1. Click **Packages**.
2. Click **Add Package**.
3. Enter package name, description, estimated duration, price and status.
4. Click **Save Package**.
5. Use **Edit** to update package details.
6. Use **Delete** only if the package has no booking record. Otherwise, set it to Inactive.

## Admin: Manage Bookings
1. Click **Bookings**.
2. Use the filter form to search by customer, plate number, service type, date or status.
3. Click **View** to see details.
4. Click **Approve**, **Reject Booking**, or **Mark Completed**.
5. The system records the action in the status log.

## Admin: Export PDF Report
1. Go to **Bookings**.
2. Apply filters if required.
3. Click **Export PDF**.
4. The PDF includes booking ID, customer name, vehicle plate, service type, date, time, status and total price.

## Customer Registration
1. Click **Register as customer**.
2. Fill name, email, phone, password and password confirmation.
3. Submit the form.
4. The system redirects to the customer dashboard.

## Customer: Add Vehicle
1. Click **My Vehicles**.
2. Click **Add Vehicle**.
3. Fill plate number, brand, model, year and color.
4. Click **Save Vehicle**.

## Customer: Create Booking
1. Click **Create Booking**.
2. Select vehicle and service package.
3. Choose preferred service date and time.
4. Add notes if needed.
5. Submit the booking.
6. The status will be **Pending** until admin approval.

## Customer: Edit or Cancel Booking
1. Open **My Bookings**.
2. Click **View**.
3. If status is Pending, click **Edit** to change booking details.
4. If status is Pending or Approved, click **Cancel Booking** to cancel.

```

## `package.json`

```json
{
    "private": true,
    "scripts": {
        "dev": "vite",
        "build": "vite build"
    },
    "devDependencies": {
        "@vitejs/plugin-vue": "latest",
        "axios": "latest",
        "laravel-vite-plugin": "latest",
        "vite": "latest"
    }
}

```

## `public/.htaccess`

```
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

```

## `public/index.php`

```php
<?php

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);

```

## `resources/views/admin/bookings/index.blade.php`

```php
@extends('layouts.app')
@section('title', 'Manage Bookings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Manage Bookings</h1>
    <a href="{{ route('admin.reports.bookings.pdf', request()->query()) }}" class="btn btn-danger btn-rounded"><i class="bi bi-filetype-pdf me-1"></i>Export PDF</a>
</div>
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-2"><input name="customer" class="form-control" value="{{ request('customer') }}" placeholder="Customer name"></div>
        <div class="col-md-2"><input name="plate_number" class="form-control" value="{{ request('plate_number') }}" placeholder="Plate number"></div>
        <div class="col-md-2"><input name="service_type" class="form-control" value="{{ request('service_type') }}" placeholder="Service type"></div>
        <div class="col-md-2"><input type="date" name="preferred_date" class="form-control" value="{{ request('preferred_date') }}"></div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-dark">Filter</button></div>
    </form>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>ID</th><th>Customer</th><th>Plate</th><th>Package</th><th>Date</th><th>Time</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->user->name }}</td>
                    <td>{{ $booking->vehicle->plate_number }}</td>
                    <td>{{ $booking->servicePackage->package_name }}</td>
                    <td>{{ $booking->preferred_date->format('d M Y') }}</td>
                    <td>{{ substr($booking->preferred_time,0,5) }}</td>
                    <td>RM {{ number_format($booking->total_price, 2) }}</td>
                    <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                    <td class="text-end"><a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-dark">View</a></td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted">No bookings found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $bookings->links() }}
</div>
@endsection

```

## `resources/views/admin/bookings/show.blade.php`

```php
@extends('layouts.app')
@section('title', 'Admin Booking Detail')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Booking #{{ $booking->id }}</h1>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">Back</a>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 mb-4">
            <h2 class="h5 fw-bold mb-3">Booking Detail</h2>
            <dl class="row mb-0">
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">@include('partials.status-badge', ['status' => $booking->status])</dd>
                <dt class="col-sm-4">Customer</dt><dd class="col-sm-8">{{ $booking->user->name }} ({{ $booking->user->email }})</dd>
                <dt class="col-sm-4">Vehicle</dt><dd class="col-sm-8">{{ $booking->vehicle->plate_number }} - {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }} {{ $booking->vehicle->year }}</dd>
                <dt class="col-sm-4">Service Package</dt><dd class="col-sm-8">{{ $booking->servicePackage->package_name }}</dd>
                <dt class="col-sm-4">Date & Time</dt><dd class="col-sm-8">{{ $booking->preferred_date->format('d M Y') }} at {{ substr($booking->preferred_time,0,5) }}</dd>
                <dt class="col-sm-4">Total Price</dt><dd class="col-sm-8">RM {{ number_format($booking->total_price, 2) }}</dd>
                <dt class="col-sm-4">Customer Notes</dt><dd class="col-sm-8">{{ $booking->additional_notes ?: '-' }}</dd>
                <dt class="col-sm-4">Admin Remarks</dt><dd class="col-sm-8">{{ $booking->admin_remarks ?: '-' }}</dd>
            </dl>
        </div>

        <div class="card p-4">
            <h2 class="h5 fw-bold mb-3">Admin Actions</h2>
            <div class="d-flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.bookings.approve', $booking) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-primary" @disabled($booking->status === 'Cancelled' || $booking->status === 'Completed')>Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-success" @disabled($booking->status === 'Cancelled' || $booking->status === 'Rejected')>Mark Completed</button>
                </form>
            </div>
            <form method="POST" action="{{ route('admin.bookings.reject', $booking) }}" class="mt-3">
                @csrf @method('PATCH')
                <label class="form-label">Reject Remarks</label>
                <textarea name="admin_remarks" class="form-control mb-2" rows="3" placeholder="Reason for rejection"></textarea>
                <button class="btn btn-danger" @disabled($booking->status === 'Cancelled' || $booking->status === 'Completed')>Reject Booking</button>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <h2 class="h5 fw-bold mb-3">Status Log</h2>
            @forelse($booking->statusLogs as $log)
                <div class="border-start ps-3 mb-3">
                    <div class="fw-bold">{{ $log->from_status ?: 'New' }} → {{ $log->to_status }}</div>
                    <div class="small text-muted">{{ $log->created_at->format('d M Y h:i A') }} by {{ $log->changedBy->name ?? 'System' }}</div>
                    <div class="small">{{ $log->remarks }}</div>
                </div>
            @empty
                <p class="text-muted">No status logs.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

```

## `resources/views/admin/customers/index.blade.php`

```php
@extends('layouts.app')
@section('title', 'Manage Customers')
@section('content')
<h1 class="page-title mb-3">Manage Customers</h1>
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-10"><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name, email or phone"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-dark">Search</button></div>
    </form>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Vehicles</th><th>Bookings</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone ?: '-' }}</td>
                    <td>{{ $customer->vehicles_count }}</td>
                    <td>{{ $customer->bookings_count }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No customers found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $customers->links() }}
</div>
@endsection

```

## `resources/views/admin/service_packages/_form.blade.php`

```php
<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Package Name</label>
        <input type="text" name="package_name" class="form-control" value="{{ old('package_name', $servicePackage->package_name ?? '') }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="Active" @selected(old('status', $servicePackage->status ?? 'Active') === 'Active')>Active</option>
            <option value="Inactive" @selected(old('status', $servicePackage->status ?? '') === 'Inactive')>Inactive</option>
        </select>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4">{{ old('description', $servicePackage->description ?? '') }}</textarea>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Estimated Duration (minutes)</label>
        <input type="number" name="estimated_duration" class="form-control" value="{{ old('estimated_duration', $servicePackage->estimated_duration ?? '') }}" min="15" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Price (RM)</label>
        <input type="number" name="price" class="form-control" step="0.01" value="{{ old('price', $servicePackage->price ?? '') }}" min="0" required>
    </div>
</div>

```

## `resources/views/admin/service_packages/create.blade.php`

```php
@extends('layouts.app')
@section('title', 'Add Service Package')
@section('content')
<h1 class="page-title mb-3">Add Service Package</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('admin.service-packages.store') }}">
        @csrf
        @include('admin.service_packages._form')
        <div class="d-flex gap-2">
            <button class="btn btn-dark">Save Package</button>
            <a href="{{ route('admin.service-packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

```

## `resources/views/admin/service_packages/edit.blade.php`

```php
@extends('layouts.app')
@section('title', 'Edit Service Package')
@section('content')
<h1 class="page-title mb-3">Edit Service Package</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('admin.service-packages.update', $servicePackage) }}">
        @csrf @method('PUT')
        @include('admin.service_packages._form', ['servicePackage' => $servicePackage])
        <div class="d-flex gap-2">
            <button class="btn btn-dark">Update Package</button>
            <a href="{{ route('admin.service-packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

```

## `resources/views/admin/service_packages/index.blade.php`

```php
@extends('layouts.app')
@section('title', 'Manage Service Packages')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Manage Service Packages</h1>
    <a href="{{ route('admin.service-packages.create') }}" class="btn btn-dark btn-rounded"><i class="bi bi-plus-circle me-1"></i>Add Package</a>
</div>
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-7"><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search package name"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="Active" @selected(request('status') === 'Active')>Active</option>
                <option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option>
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-dark">Filter</button></div>
    </form>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Package</th><th>Duration</th><th>Price</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($packages as $package)
                <tr>
                    <td><div class="fw-bold">{{ $package->package_name }}</div><div class="small text-muted">{{ Str::limit($package->description, 80) }}</div></td>
                    <td>{{ $package->estimated_duration }} min</td>
                    <td>RM {{ number_format($package->price, 2) }}</td>
                    <td><span class="badge {{ $package->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $package->status }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.service-packages.edit', $package) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.service-packages.destroy', $package) }}" class="d-inline" onsubmit="return confirm('Delete this package?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">No packages found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $packages->links() }}
</div>
@endsection

```

## `resources/views/admin/vehicles/index.blade.php`

```php
@extends('layouts.app')
@section('title', 'Manage Vehicles')
@section('content')
<h1 class="page-title mb-3">Manage Vehicles</h1>
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">
        <div class="col-md-10"><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by customer, plate, brand or model"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-dark">Search</button></div>
    </form>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Customer</th><th>Plate</th><th>Brand</th><th>Model</th><th>Year</th><th>Color</th></tr></thead>
            <tbody>
            @forelse($vehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle->user->name }}</td>
                    <td class="fw-bold">{{ $vehicle->plate_number }}</td>
                    <td>{{ $vehicle->brand }}</td>
                    <td>{{ $vehicle->model }}</td>
                    <td>{{ $vehicle->year }}</td>
                    <td>{{ $vehicle->color }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No vehicles found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $vehicles->links() }}
</div>
@endsection

```

## `resources/views/auth/login.blade.php`

```php
@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center auth-shell">
    <div class="col-lg-5 col-md-7">
        <div class="card p-4">
            <div class="text-center mb-4">
                <div class="display-5"><i class="bi bi-tools"></i></div>
                <h1 class="h3 fw-bold">Car Service Booking</h1>
                <p class="text-muted mb-0">Login to manage your service bookings.</p>
            </div>
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button class="btn btn-dark w-100" type="submit">Login</button>
            </form>
            <hr>
            <p class="text-center mb-0">No account? <a href="{{ route('register') }}">Register as customer</a></p>
            <div class="small text-muted mt-3">
                Admin: admin@example.com / password<br>
                Customer: customer@example.com / password
            </div>
        </div>
    </div>
</div>
@endsection

```

## `resources/views/auth/register.blade.php`

```php
@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center auth-shell">
    <div class="col-lg-6 col-md-8">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h1 class="h3 fw-bold">Create Customer Account</h1>
                <p class="text-muted mb-0">Register to add vehicles and book service appointments.</p>
            </div>
            <form method="POST" action="{{ route('register.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <button class="btn btn-dark w-100" type="submit">Register</button>
            </form>
            <hr>
            <p class="text-center mb-0">Already registered? <a href="{{ route('login') }}">Login</a></p>
        </div>
    </div>
</div>
@endsection

```

## `resources/views/customer/bookings/_form.blade.php`

```php
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Customer Name</label>
        <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Vehicle</label>
        <select name="vehicle_id" class="form-select" required>
            <option value="">-- Select vehicle --</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $booking->vehicle_id ?? '') == $vehicle->id)>
                    {{ $vehicle->plate_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                </option>
            @endforeach
        </select>
        @if($vehicles->isEmpty())
            <div class="small text-danger mt-1">Please add a vehicle first.</div>
        @endif
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Service Package</label>
        <select name="service_package_id" class="form-select" required>
            <option value="">-- Select service package --</option>
            @foreach($packages as $package)
                <option value="{{ $package->id }}" @selected(old('service_package_id', $booking->service_package_id ?? '') == $package->id)>
                    {{ $package->package_name }} - RM {{ number_format($package->price, 2) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Preferred Date</label>
        <input type="date" name="preferred_date" class="form-control" value="{{ old('preferred_date', isset($booking) ? $booking->preferred_date->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Preferred Time</label>
        <input type="time" name="preferred_time" class="form-control" value="{{ old('preferred_time', isset($booking) ? substr($booking->preferred_time,0,5) : '') }}" required>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">Additional Notes</label>
        <textarea name="additional_notes" class="form-control" rows="4" placeholder="Describe any issue or request">{{ old('additional_notes', $booking->additional_notes ?? '') }}</textarea>
    </div>
</div>

```

## `resources/views/customer/bookings/create.blade.php`

```php
@extends('layouts.app')
@section('title', 'Create Booking')
@section('content')
<h1 class="page-title mb-3">Create Booking</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('customer.bookings.store') }}">
        @csrf
        @include('customer.bookings._form')
        <div class="d-flex gap-2">
            <button class="btn btn-dark" @disabled($vehicles->isEmpty())>Submit Booking</button>
            <a href="{{ route('customer.bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

```

## `resources/views/customer/bookings/edit.blade.php`

```php
@extends('layouts.app')
@section('title', 'Edit Booking')
@section('content')
<h1 class="page-title mb-3">Edit Booking #{{ $booking->id }}</h1>
<div class="alert alert-info">Only pending bookings can be edited before admin approval.</div>
<div class="card p-4">
    <form method="POST" action="{{ route('customer.bookings.update', $booking) }}">
        @csrf @method('PUT')
        @include('customer.bookings._form', ['booking' => $booking])
        <div class="d-flex gap-2">
            <button class="btn btn-dark">Update Booking</button>
            <a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

```

## `resources/views/customer/bookings/index.blade.php`

```php
@extends('layouts.app')
@section('title', 'My Bookings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">My Bookings</h1>
    <a href="{{ route('customer.bookings.create') }}" class="btn btn-dark btn-rounded"><i class="bi bi-plus-circle me-1"></i>Create Booking</a>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>ID</th><th>Vehicle</th><th>Package</th><th>Date</th><th>Time</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->vehicle->plate_number }}</td>
                    <td>{{ $booking->servicePackage->package_name }}</td>
                    <td>{{ $booking->preferred_date->format('d M Y') }}</td>
                    <td>{{ substr($booking->preferred_time,0,5) }}</td>
                    <td>RM {{ number_format($booking->total_price, 2) }}</td>
                    <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                    <td class="text-end"><a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-sm btn-outline-dark">View</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted">No bookings found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $bookings->links() }}
</div>
@endsection

```

## `resources/views/customer/bookings/show.blade.php`

```php
@extends('layouts.app')
@section('title', 'Booking Detail')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Booking #{{ $booking->id }}</h1>
    <div class="d-flex gap-2">
        @if($booking->canBeEditedByCustomer())
            <a href="{{ route('customer.bookings.edit', $booking) }}" class="btn btn-outline-primary">Edit</a>
        @endif
        @if($booking->canBeCancelledByCustomer())
            <form method="POST" action="{{ route('customer.bookings.destroy', $booking) }}" onsubmit="return confirm('Cancel this booking?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Cancel Booking</button>
            </form>
        @endif
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4">
            <h2 class="h5 fw-bold mb-3">Booking Information</h2>
            <dl class="row mb-0">
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">@include('partials.status-badge', ['status' => $booking->status])</dd>
                <dt class="col-sm-4">Customer</dt><dd class="col-sm-8">{{ $booking->user->name }}</dd>
                <dt class="col-sm-4">Vehicle</dt><dd class="col-sm-8">{{ $booking->vehicle->plate_number }} - {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</dd>
                <dt class="col-sm-4">Service Package</dt><dd class="col-sm-8">{{ $booking->servicePackage->package_name }}</dd>
                <dt class="col-sm-4">Date & Time</dt><dd class="col-sm-8">{{ $booking->preferred_date->format('d M Y') }} at {{ substr($booking->preferred_time,0,5) }}</dd>
                <dt class="col-sm-4">Total Price</dt><dd class="col-sm-8">RM {{ number_format($booking->total_price, 2) }}</dd>
                <dt class="col-sm-4">Customer Notes</dt><dd class="col-sm-8">{{ $booking->additional_notes ?: '-' }}</dd>
                <dt class="col-sm-4">Admin Remarks</dt><dd class="col-sm-8">{{ $booking->admin_remarks ?: '-' }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <h2 class="h5 fw-bold mb-3">Status Log</h2>
            @forelse($booking->statusLogs as $log)
                <div class="border-start ps-3 mb-3">
                    <div class="fw-bold">{{ $log->from_status ?: 'New' }} → {{ $log->to_status }}</div>
                    <div class="small text-muted">{{ $log->created_at->format('d M Y h:i A') }} by {{ $log->changedBy->name ?? 'System' }}</div>
                    <div class="small">{{ $log->remarks }}</div>
                </div>
            @empty
                <p class="text-muted">No status logs.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

```

## `resources/views/customer/packages/index.blade.php`

```php
@extends('layouts.app')
@section('title', 'Service Packages')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Available Service Packages</h1>
        <p class="text-muted mb-0">Choose a service package before creating a booking.</p>
    </div>
    <a href="{{ route('customer.bookings.create') }}" class="btn btn-dark btn-rounded">Book Now</a>
</div>
<div class="row g-4">
@forelse($packages as $package)
    <div class="col-lg-4 col-md-6">
        <div class="card p-4 h-100">
            <h2 class="h5 fw-bold">{{ $package->package_name }}</h2>
            <p class="text-muted">{{ $package->description }}</p>
            <div class="mt-auto">
                <div><i class="bi bi-clock me-1"></i>{{ $package->estimated_duration }} minutes</div>
                <div class="h4 fw-bold mt-2">RM {{ number_format($package->price, 2) }}</div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12"><div class="alert alert-info">No active packages available.</div></div>
@endforelse
</div>
<div class="mt-3">{{ $packages->links() }}</div>
@endsection

```

## `resources/views/customer/vehicles/_form.blade.php`

```php
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Plate Number</label>
        <input type="text" name="plate_number" class="form-control" value="{{ old('plate_number', $vehicle->plate_number ?? '') }}" placeholder="ABC1234" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Brand</label>
        <input type="text" name="brand" class="form-control" value="{{ old('brand', $vehicle->brand ?? '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Model</label>
        <input type="text" name="model" class="form-control" value="{{ old('model', $vehicle->model ?? '') }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Year</label>
        <input type="number" name="year" class="form-control" value="{{ old('year', $vehicle->year ?? '') }}" min="1980" max="{{ date('Y') + 1 }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Color</label>
        <input type="text" name="color" class="form-control" value="{{ old('color', $vehicle->color ?? '') }}" required>
    </div>
</div>

```

## `resources/views/customer/vehicles/create.blade.php`

```php
@extends('layouts.app')
@section('title', 'Add Vehicle')
@section('content')
<h1 class="page-title mb-3">Add Vehicle</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('customer.vehicles.store') }}">
        @csrf
        @include('customer.vehicles._form')
        <div class="d-flex gap-2">
            <button class="btn btn-dark">Save Vehicle</button>
            <a href="{{ route('customer.vehicles.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

```

## `resources/views/customer/vehicles/edit.blade.php`

```php
@extends('layouts.app')
@section('title', 'Edit Vehicle')
@section('content')
<h1 class="page-title mb-3">Edit Vehicle</h1>
<div class="card p-4">
    <form method="POST" action="{{ route('customer.vehicles.update', $vehicle) }}">
        @csrf @method('PUT')
        @include('customer.vehicles._form', ['vehicle' => $vehicle])
        <div class="d-flex gap-2">
            <button class="btn btn-dark">Update Vehicle</button>
            <a href="{{ route('customer.vehicles.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

```

## `resources/views/customer/vehicles/index.blade.php`

```php
@extends('layouts.app')
@section('title', 'My Vehicles')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">My Vehicles</h1>
    <a href="{{ route('customer.vehicles.create') }}" class="btn btn-dark btn-rounded"><i class="bi bi-plus-circle me-1"></i>Add Vehicle</a>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Plate</th><th>Brand</th><th>Model</th><th>Year</th><th>Color</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($vehicles as $vehicle)
                <tr>
                    <td class="fw-bold">{{ $vehicle->plate_number }}</td>
                    <td>{{ $vehicle->brand }}</td>
                    <td>{{ $vehicle->model }}</td>
                    <td>{{ $vehicle->year }}</td>
                    <td>{{ $vehicle->color }}</td>
                    <td class="text-end">
                        <a href="{{ route('customer.vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('customer.vehicles.destroy', $vehicle) }}" class="d-inline" onsubmit="return confirm('Delete this vehicle?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No vehicles registered.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $vehicles->links() }}
</div>
@endsection

```

## `resources/views/dashboards/admin.blade.php`

```php
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="text-muted mb-0">Monitor customers, vehicles, packages and booking workflow.</p>
    </div>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-dark btn-rounded"><i class="bi bi-search me-1"></i>Search Bookings</a>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Customers', $totalCustomers, 'bi-people'],
        ['Vehicles', $totalVehicles, 'bi-car-front'],
        ['Packages', $totalPackages, 'bi-box-seam'],
        ['Bookings', $totalBookings, 'bi-calendar-check'],
        ['Pending', $pendingBookings, 'bi-hourglass-split'],
        ['Completed', $completedBookings, 'bi-check2-circle'],
    ] as [$label, $value, $icon])
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stat-card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">{{ $label }}</div>
                        <div class="h3 fw-bold">{{ $value }}</div>
                    </div>
                    <i class="bi {{ $icon }} fs-3 text-secondary"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card p-3">
    <h2 class="h5 fw-bold mb-3">Latest Bookings</h2>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>ID</th><th>Customer</th><th>Plate</th><th>Package</th><th>Date</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($latestBookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->user->name }}</td>
                    <td>{{ $booking->vehicle->plate_number }}</td>
                    <td>{{ $booking->servicePackage->package_name }}</td>
                    <td>{{ $booking->preferred_date->format('d M Y') }} {{ substr($booking->preferred_time,0,5) }}</td>
                    <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                    <td><a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-dark">View</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">No bookings yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

```

## `resources/views/dashboards/customer.blade.php`

```php
@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Customer Dashboard</h1>
        <p class="text-muted mb-0">Book services and manage your registered vehicles.</p>
    </div>
    <a href="{{ route('customer.bookings.create') }}" class="btn btn-dark btn-rounded"><i class="bi bi-plus-circle me-1"></i>New Booking</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card p-3 stat-card"><div class="text-muted">My Vehicles</div><div class="display-6 fw-bold">{{ $vehicleCount }}</div></div></div>
    <div class="col-md-4"><div class="card p-3 stat-card"><div class="text-muted">My Bookings</div><div class="display-6 fw-bold">{{ $bookingCount }}</div></div></div>
    <div class="col-md-4"><div class="card p-3 stat-card"><div class="text-muted">Pending Approval</div><div class="display-6 fw-bold">{{ $pendingBookings }}</div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-3 h-100">
            <h2 class="h5 fw-bold mb-3">Latest Bookings</h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Vehicle</th><th>Package</th><th>Date</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @forelse($latestBookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>{{ $booking->vehicle->plate_number }}</td>
                            <td>{{ $booking->servicePackage->package_name }}</td>
                            <td>{{ $booking->preferred_date->format('d M Y') }}</td>
                            <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                            <td><a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-sm btn-outline-dark">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No bookings yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h2 class="h5 fw-bold mb-3">Popular Packages</h2>
            @foreach($activePackages as $package)
                <div class="border rounded p-3 mb-2">
                    <div class="fw-bold">{{ $package->package_name }}</div>
                    <div class="small text-muted">{{ $package->estimated_duration }} minutes</div>
                    <div class="fw-bold mt-1">RM {{ number_format($package->price, 2) }}</div>
                </div>
            @endforeach
            <a href="{{ route('customer.packages.index') }}" class="btn btn-outline-dark mt-2">View All Packages</a>
        </div>
    </div>
</div>
@endsection

```

## `resources/views/layouts/app.blade.php`

```php
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Car Service Booking Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; }
        .navbar-brand { font-weight: 700; letter-spacing: .2px; }
        .card { border: 0; border-radius: 1rem; box-shadow: 0 10px 25px rgba(15, 23, 42, .06); }
        .stat-card { min-height: 120px; }
        .page-title { font-weight: 800; color: #1f2937; }
        .badge-status { font-size: .8rem; }
        .table td, .table th { vertical-align: middle; }
        .auth-shell { min-height: 100vh; display: flex; align-items: center; }
        .btn-rounded { border-radius: 999px; }
        .nav-link.active { font-weight: 700; }
    </style>
</head>
<body>
@if(auth()->check())
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}"><i class="bi bi-tools me-2"></i>Car Service Booking</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @if(auth()->user()->isAdmin())
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.customers.index') }}">Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.vehicles.index') }}">Vehicles</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.service-packages.index') }}">Packages</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.vehicles.index') }}">My Vehicles</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.packages.index') }}">Packages</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.bookings.index') }}">My Bookings</a></li>
                @endif
            </ul>
            <div class="d-flex align-items-center gap-3 text-white">
                <span class="small">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm btn-rounded" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>
@endif

<main class="container py-4">
    @include('partials.flash')
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

```

## `resources/views/partials/flash.blade.php`

```php
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

```

## `resources/views/partials/status-badge.blade.php`

```php
@php
    $classes = [
        'Pending' => 'bg-warning text-dark',
        'Approved' => 'bg-primary',
        'Rejected' => 'bg-danger',
        'Completed' => 'bg-success',
        'Cancelled' => 'bg-secondary',
    ];
@endphp
<span class="badge badge-status {{ $classes[$status] ?? 'bg-dark' }}">{{ $status }}</span>

```

## `resources/views/pdf/bookings.blade.php`

```php
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 20px; margin-bottom: 5px; }
        .meta { margin-bottom: 15px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 7px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Car Service Booking Report</h1>
    <div class="meta">
        Generated at: {{ $generatedAt->format('d M Y h:i A') }}<br>
        Filters:
        Customer={{ $filters['customer'] ?? 'All' }},
        Plate={{ $filters['plate_number'] ?? 'All' }},
        Service={{ $filters['service_type'] ?? 'All' }},
        Date={{ $filters['preferred_date'] ?? 'All' }},
        Status={{ $filters['status'] ?? 'All' }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Customer Name</th>
                <th>Vehicle Plate</th>
                <th>Service Type</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th class="right">Total Price (RM)</th>
            </tr>
        </thead>
        <tbody>
        @forelse($bookings as $booking)
            <tr>
                <td>#{{ $booking->id }}</td>
                <td>{{ $booking->user->name }}</td>
                <td>{{ $booking->vehicle->plate_number }}</td>
                <td>{{ $booking->servicePackage->package_name }}</td>
                <td>{{ $booking->preferred_date->format('d M Y') }}</td>
                <td>{{ substr($booking->preferred_time,0,5) }}</td>
                <td>{{ $booking->status }}</td>
                <td class="right">{{ number_format($booking->total_price, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="8">No booking records found.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>

```

## `routes/api.php`

```php
<?php

use Illuminate\Support\Facades\Route;

// API routes can be added here for future REST API expansion.
Route::get('/health', fn() => ['status' => 'ok']);

```

## `routes/console.php`

```php
<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about-project', function () {
    $this->info('Car Service Booking Management System');
});

```

## `routes/web.php`

```php
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
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

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

```

## `storage/app/public/.gitignore`

```
*
!.gitignore

```

## `storage/framework/cache/data/.gitignore`

```
*
!.gitignore

```

## `storage/framework/sessions/.gitignore`

```
*
!.gitignore

```

## `storage/framework/testing/.gitignore`

```
*
!.gitignore

```

## `storage/framework/views/.gitignore`

```
*
!.gitignore

```

## `storage/logs/.gitignore`

```
*
!.gitignore

```

## `tests/CreatesApplication.php`

```php
<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        return $app;
    }
}

```

## `tests/TestCase.php`

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
}

```
