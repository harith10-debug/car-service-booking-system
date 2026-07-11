# DH Motorsport Car Service Booking System

A complete Laravel 10 web application prepared for the IMS566 group project requirements. The system digitalises a car service booking process with customer booking, admin approval, payment, receipt, nearby workshop finder, subscription plan, reports and responsive UI.

## Stack

- PHP 8.1+
- Laravel 10
- MySQL
- Blade + Bootstrap 5
- Laravel DomPDF
- Custom Laravel authentication and role-based middleware

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

### Core IMS566 Requirements

- Authentication: register, login, logout and role-based redirects.
- CRUD: customer vehicles, customer bookings, admin service packages, admin workshops and admin subscription plans.
- Search/filter: bookings, customers, vehicles, service packages, workshops, payments and subscriptions.
- PDF export: booking report, payment sales report and customer payment receipt.
- Responsive Bootstrap 5 UI with consistent navigation and dark motorsport theme.
- Normalized database tables with foreign keys, indexes and validation.

### Added Customer Features

- Make payment for approved/completed bookings.
- Download official PDF receipt after payment.
- Find nearby workshops with Shah Alam default distance and browser location sorting.
- Select a preferred workshop during booking.
- Subscribe to membership plans for discounts and priority booking.
- View payment history and subscription history.

### Added Admin Features

- Booking acceptance monitor with quick accept action.
- Payment and sales monitor with totals, average payment and payment method breakdown.
- Manage workshop locations for the customer finder.
- Monitor subscriptions and subscriber revenue.
- Manage subscription plans and benefits.

## Useful Documentation

- `docs/report.md`
- `docs/user-manual.md`
- `docs/test-cases.md`
- `docs/database-schema.sql`
- `docs/erd.md`
