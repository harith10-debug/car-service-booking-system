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
