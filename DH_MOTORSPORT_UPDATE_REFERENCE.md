# DH Motorsport UI/Branding Update Reference

This file contains the full updated code for every file modified in the DH Motorsport rebrand/theme update. Backend controllers, database migrations, models, routes and CRUD logic were not changed.

## `README.md`

```markdown
# DH Motorsport

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

## `.env.example`

```env
APP_NAME="DH Motorsport"
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

## `config/app.php`

```php
<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'name' => env('APP_NAME', 'DH Motorsport'),
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
        'name' => env('MAIL_FROM_NAME', 'DH Motorsport'),
    ],
];
```

## `public/css/custom.css`

```css
:root {
    --brand: #e10600;
    --brand-dark: #a60400;
    --brand-soft: rgba(225, 6, 0, .14);
    --accent: #ffd000;
    --accent-dark: #d6a900;
    --dark: #050505;
    --dark-2: #0b0f14;
    --muted: #aab3c2;
    --line: rgba(255, 255, 255, .12);
    --surface: #111820;
    --surface-2: #171f29;
    --page: #07090d;
    --success-soft: rgba(34, 197, 94, .12);
    --shadow-sm: 0 14px 32px rgba(0, 0, 0, .30);
    --shadow-md: 0 22px 60px rgba(0, 0, 0, .46);
    --glow-red: 0 0 0 1px rgba(225, 6, 0, .28), 0 18px 55px rgba(225, 6, 0, .20);
    --radius-lg: 1.25rem;
    --radius-md: 1rem;
}

* {
    scroll-behavior: smooth;
}

body {
    background:
        radial-gradient(circle at top left, rgba(225, 6, 0, .22), transparent 28%),
        radial-gradient(circle at 85% 10%, rgba(255, 208, 0, .10), transparent 26%),
        linear-gradient(180deg, #050505, var(--page));
    color: #f8fafc;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

body::before {
    content: "";
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: -1;
    background-image:
        linear-gradient(135deg, rgba(255,255,255,.035) 25%, transparent 25%),
        linear-gradient(225deg, rgba(255,255,255,.035) 25%, transparent 25%);
    background-size: 38px 38px;
    opacity: .22;
}

a {
    color: var(--accent);
    transition: all .2s ease;
}

a:hover {
    color: #fff0a0;
}

.text-muted {
    color: var(--muted) !important;
}

.text-secondary {
    color: #d4d9e2 !important;
}

.text-success,
.hero-trust i {
    color: var(--accent) !important;
}

.app-navbar {
    background: rgba(5, 5, 5, .90);
    border-bottom: 1px solid rgba(225, 6, 0, .35);
    backdrop-filter: blur(16px);
    box-shadow: 0 12px 36px rgba(0, 0, 0, .45);
}

.navbar-brand {
    font-weight: 900;
    letter-spacing: .02em;
    color: #fff;
    text-transform: uppercase;
}

.navbar-brand:hover {
    color: var(--accent);
}

.navbar-toggler {
    border-color: rgba(255, 208, 0, .5);
}

.navbar-toggler:focus {
    box-shadow: 0 0 0 .2rem rgba(255, 208, 0, .20);
}

.navbar-toggler-icon {
    filter: invert(1) brightness(1.4);
}

.brand-icon,
.landing-brand-icon {
    width: 40px;
    height: 40px;
    display: inline-grid;
    place-items: center;
    border-radius: 12px;
    color: var(--accent);
    background:
        linear-gradient(135deg, rgba(225, 6, 0, .95), rgba(0, 0, 0, .95)),
        #000;
    border: 1px solid rgba(255, 208, 0, .45);
    box-shadow: 0 12px 28px rgba(225, 6, 0, .35);
}

.nav-pills-soft .nav-link {
    color: #d8dee9;
    border-radius: 999px;
    padding: .55rem .9rem;
    font-weight: 750;
}

.nav-pills-soft .nav-link:hover,
.nav-pills-soft .nav-link.active {
    color: var(--accent);
    background: rgba(225, 6, 0, .20);
}

.user-chip {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .45rem .75rem;
    border: 1px solid rgba(255, 208, 0, .25);
    border-radius: 999px;
    background: rgba(17, 24, 32, .90);
    font-size: .875rem;
    font-weight: 700;
    color: #edf2f7;
}

.role-dot {
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: var(--accent);
    box-shadow: 0 0 14px rgba(255, 208, 0, .75);
}

.card {
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    color: #f8fafc;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, .055), rgba(255, 255, 255, .025)),
        var(--surface);
    box-shadow: var(--shadow-sm);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}

.card:hover {
    transform: translateY(-2px);
    border-color: rgba(225, 6, 0, .42);
    box-shadow: var(--shadow-md);
}

.stat-card {
    min-height: 120px;
    overflow: hidden;
    position: relative;
}

.stat-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--brand), var(--accent));
}

.stat-card::after {
    content: "";
    position: absolute;
    width: 100px;
    height: 100px;
    right: -35px;
    bottom: -35px;
    border-radius: 999px;
    background: rgba(225, 6, 0, .18);
}

.page-title {
    font-weight: 900;
    color: #fff;
    letter-spacing: -.035em;
    text-transform: uppercase;
}

.badge-status {
    font-size: .8rem;
}

.table {
    margin-bottom: 0;
    color: #eef2f7;
}

.table td,
.table th {
    vertical-align: middle;
    border-color: rgba(255, 255, 255, .08);
}

.table thead th {
    background: rgba(0, 0, 0, .35);
    color: var(--accent);
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: .05em;
    border-bottom-color: rgba(255, 208, 0, .24);
}

.table-hover tbody tr:hover,
.table-hover>tbody>tr:hover>* {
    color: #fff;
    background-color: rgba(225, 6, 0, .12);
}

.auth-shell {
    min-height: calc(100vh - 3rem);
    display: flex;
    align-items: center;
}

.auth-shell .card {
    border: 1px solid rgba(255, 208, 0, .18);
    box-shadow: var(--glow-red);
}

.auth-brand-mark {
    color: var(--accent);
    text-shadow: 0 0 28px rgba(255, 208, 0, .36);
}

.btn-rounded {
    border-radius: 999px;
}

.btn-dark,
.btn-outline-dark:hover,
.btn-outline-secondary:hover {
    background: linear-gradient(135deg, #0b0f14, #000);
    border-color: rgba(255, 208, 0, .35);
    color: #fff;
    box-shadow: 0 12px 24px rgba(0, 0, 0, .35);
}

.btn-dark:hover {
    background: linear-gradient(135deg, var(--brand-dark), #000);
    border-color: var(--brand);
    color: #fff;
    transform: translateY(-1px);
}

.btn-brand {
    background: linear-gradient(135deg, var(--brand), #7a0300);
    border: 1px solid rgba(255, 208, 0, .30);
    color: #fff;
    box-shadow: 0 16px 34px rgba(225, 6, 0, .34);
}

.btn-brand:hover,
.btn-brand:focus {
    color: #080808;
    background: linear-gradient(135deg, var(--accent), #ffb300);
    border-color: var(--accent);
    transform: translateY(-2px);
}

.btn-outline-brand,
.btn-outline-dark,
.btn-outline-secondary,
.btn-outline-primary {
    border-color: rgba(255, 208, 0, .45);
    color: var(--accent);
    background: rgba(0, 0, 0, .25);
}

.btn-outline-brand:hover,
.btn-outline-dark:hover,
.btn-outline-secondary:hover,
.btn-outline-primary:hover {
    background: var(--brand);
    border-color: var(--brand);
    color: #fff;
    transform: translateY(-1px);
}

.btn-danger,
.btn-outline-danger:hover {
    background: var(--brand);
    border-color: var(--brand);
}

.btn-light {
    background: var(--accent);
    border-color: var(--accent);
    color: #080808;
    font-weight: 800;
}

.btn-light:hover {
    background: #fff;
    border-color: #fff;
    color: #080808;
    transform: translateY(-1px);
}

.form-label {
    color: #edf2f7;
    font-weight: 700;
}

.form-control,
.form-select {
    border-radius: .85rem;
    border-color: rgba(255, 255, 255, .14);
    padding: .72rem .9rem;
    color: #fff;
    background-color: rgba(0, 0, 0, .34);
}

.form-control::placeholder {
    color: #768195;
}

.form-control:focus,
.form-select:focus {
    color: #fff;
    background-color: rgba(0, 0, 0, .50);
    border-color: rgba(255, 208, 0, .78);
    box-shadow: 0 0 0 .25rem rgba(255, 208, 0, .13);
}

.form-select option {
    color: #111;
    background: #fff;
}

.form-check-input {
    background-color: #111820;
    border-color: rgba(255, 208, 0, .40);
}

.form-check-input:checked {
    background-color: var(--brand);
    border-color: var(--accent);
}

.alert {
    border: 1px solid rgba(255, 255, 255, .10);
    border-radius: 1rem;
    box-shadow: var(--shadow-sm);
}

.alert-success {
    color: #eafff2;
    background: rgba(34, 197, 94, .14);
}

.alert-danger {
    color: #ffe7e7;
    background: rgba(225, 6, 0, .18);
}

.alert-info {
    color: #e8f6ff;
    background: rgba(14, 165, 233, .14);
}

.border,
.rounded {
    border-color: rgba(255, 255, 255, .12) !important;
}

/* Landing page */
.landing-main {
    padding: 0;
}

.landing-page {
    background: var(--page);
    overflow-x: hidden;
}

.landing-nav {
    position: sticky;
    top: 0;
    z-index: 1020;
    background: rgba(0, 0, 0, .88);
    backdrop-filter: blur(18px);
    border-bottom: 1px solid rgba(225, 6, 0, .40);
}

.landing-nav .nav-link {
    color: #e4e8ee;
    font-weight: 700;
}

.landing-nav .nav-link:hover {
    color: var(--accent);
}

.hero-section {
    position: relative;
    padding: 6.5rem 0 4.5rem;
    overflow: hidden;
    background:
        radial-gradient(circle at 10% 15%, rgba(225, 6, 0, .32), transparent 30%),
        radial-gradient(circle at 90% 10%, rgba(255, 208, 0, .15), transparent 25%),
        linear-gradient(135deg, #050505 0%, #101720 52%, #050505 100%);
}

.hero-racing-stripe {
    position: absolute;
    inset: auto -10% 10% auto;
    width: 65%;
    height: 140px;
    transform: rotate(-11deg);
    background:
        linear-gradient(90deg, transparent 0 8%, rgba(225, 6, 0, .75) 8% 22%, transparent 22% 27%, rgba(255, 208, 0, .92) 27% 34%, transparent 34% 100%);
    opacity: .36;
    filter: blur(.2px);
}

.hero-badge,
.section-badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .45rem .85rem;
    border-radius: 999px;
    background: rgba(225, 6, 0, .18);
    border: 1px solid rgba(255, 208, 0, .20);
    color: var(--accent);
    font-weight: 800;
    font-size: .875rem;
}

.hero-title {
    font-size: clamp(2.4rem, 5vw, 4.8rem);
    line-height: .98;
    letter-spacing: -.06em;
    font-weight: 950;
    color: #fff;
    text-transform: uppercase;
}

.hero-title span {
    color: var(--accent);
    text-shadow: 0 0 35px rgba(255, 208, 0, .24);
}

.hero-copy {
    max-width: 640px;
    color: #c8d0dc;
    font-size: 1.1rem;
    line-height: 1.75;
}

.hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .85rem;
}

.hero-trust {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    color: #d6dde8;
    font-weight: 750;
}

.hero-trust span {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
}

.hero-visual {
    position: relative;
    max-width: 520px;
    margin-inline: auto;
}

.hero-car-card {
    position: relative;
    padding: 1.5rem;
    border: 1px solid rgba(255, 208, 0, .22);
    border-radius: 2rem;
    background:
        linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.025)),
        rgba(10, 14, 20, .86);
    box-shadow: var(--shadow-md), 0 0 55px rgba(225, 6, 0, .16);
    backdrop-filter: blur(16px);
}

.hero-card-topline {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    color: var(--accent);
    text-transform: uppercase;
    font-weight: 900;
    letter-spacing: .08em;
    font-size: .8rem;
}

.race-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--brand);
    box-shadow: 0 0 18px rgba(225, 6, 0, .8);
}

.car-illustration {
    min-height: 250px;
    display: grid;
    place-items: center;
    border-radius: 1.5rem;
    background:
        linear-gradient(135deg, rgba(225, 6, 0, .28), rgba(255, 208, 0, .10)),
        linear-gradient(180deg, #0e141d, #080a0d);
    border: 1px solid rgba(255, 255, 255, .10);
}

.car-illustration i {
    font-size: 8rem;
    color: #fff;
    filter: drop-shadow(0 18px 22px rgba(225, 6, 0, .42));
}

.floating-service-card {
    position: absolute;
    left: -18px;
    bottom: 30px;
    width: min(250px, 70%);
    padding: 1rem;
    border-radius: 1.25rem;
    background: rgba(10, 14, 20, .96);
    color: #fff;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(255, 208, 0, .22);
    animation: floatCard 4s ease-in-out infinite;
}

.floating-status-card {
    position: absolute;
    right: -12px;
    top: 28px;
    padding: .85rem 1rem;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--brand), #6c0200);
    color: #fff;
    font-weight: 850;
    box-shadow: var(--shadow-md);
    animation: floatCard 4.5s ease-in-out infinite;
}

@keyframes floatCard {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.section-padding {
    padding: 5rem 0;
}

.section-muted {
    background:
        linear-gradient(135deg, rgba(225, 6, 0, .10), transparent 45%),
        #0b0f14;
}

.section-title {
    font-weight: 950;
    letter-spacing: -.04em;
    color: #fff;
    text-transform: uppercase;
}

.section-copy {
    color: var(--muted);
    max-width: 680px;
    line-height: 1.7;
}

.feature-card,
.process-card,
.benefit-card,
.landing-service-card {
    height: 100%;
    padding: 1.35rem;
    border: 1px solid rgba(255, 255, 255, .10);
    border-radius: var(--radius-lg);
    color: #fff;
    background:
        linear-gradient(180deg, rgba(255,255,255,.055), rgba(255,255,255,.025)),
        var(--surface);
    box-shadow: var(--shadow-sm);
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background .22s ease;
}

.feature-card:hover,
.process-card:hover,
.benefit-card:hover,
.landing-service-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-md);
    border-color: rgba(255, 208, 0, .35);
}

.feature-icon,
.process-number,
.benefit-icon,
.service-icon {
    width: 48px;
    height: 48px;
    display: inline-grid;
    place-items: center;
    border-radius: 16px;
    background: rgba(225, 6, 0, .18);
    border: 1px solid rgba(255, 208, 0, .22);
    color: var(--accent);
    font-size: 1.35rem;
    font-weight: 900;
}

.process-number {
    color: #080808;
    background: linear-gradient(135deg, var(--accent), #ffb300);
}

.landing-service-card {
    cursor: pointer;
    position: relative;
}

.landing-service-card.selected,
.service-option-card.selected {
    border-color: var(--accent);
    background:
        linear-gradient(180deg, rgba(255, 208, 0, .16), rgba(225, 6, 0, .10)),
        var(--surface-2);
    box-shadow: 0 20px 55px rgba(255, 208, 0, .13), 0 0 0 1px rgba(255, 208, 0, .25);
}

.landing-service-card.selected::after,
.service-option-card.selected::after {
    content: "\F26A";
    font-family: "bootstrap-icons";
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 30px;
    height: 30px;
    display: grid;
    place-items: center;
    border-radius: 999px;
    color: #080808;
    background: var(--accent);
    box-shadow: 0 0 20px rgba(255, 208, 0, .4);
}

.cta-panel {
    padding: clamp(2rem, 5vw, 4rem);
    border-radius: 2rem;
    color: #fff;
    background:
        radial-gradient(circle at top right, rgba(255, 208, 0, .26), transparent 32%),
        linear-gradient(135deg, #070707, #1a0606 54%, #0e141d);
    border: 1px solid rgba(255, 208, 0, .22);
    box-shadow: var(--shadow-md);
}

.landing-footer {
    border-top: 1px solid rgba(225, 6, 0, .28);
    background: rgba(0, 0, 0, .72);
}

.footer-link {
    color: #b8c0cc;
    text-decoration: none;
}

.footer-link:hover {
    color: var(--accent);
}

/* Booking service selection */
.service-selection-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
}

.service-option-card {
    position: relative;
    display: block;
    min-height: 180px;
    padding: 1.15rem;
    border: 1px solid rgba(255, 255, 255, .12);
    border-radius: 1.25rem;
    background:
        linear-gradient(180deg, rgba(255,255,255,.055), rgba(255,255,255,.025)),
        var(--surface);
    cursor: pointer;
    color: #fff;
    transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease, background .2s ease;
}

.service-option-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 208, 0, .42);
    box-shadow: var(--shadow-sm);
}

.service-option-card:active {
    transform: scale(.98);
}

.service-option-card input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.service-option-title {
    padding-right: 2rem;
    font-weight: 850;
}

.service-option-desc {
    color: var(--muted);
    font-size: .9rem;
    min-height: 42px;
}

.service-option-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
    margin-top: 1rem;
}

.price-pill {
    padding: .45rem .7rem;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--brand), #6c0200);
    color: #fff;
    font-weight: 850;
    white-space: nowrap;
}

.duration-pill {
    color: #d4d9e2;
    font-size: .875rem;
    font-weight: 700;
}

.selected-service-helper {
    display: none;
}

.selected-service-helper.show {
    display: block;
}

.pagination .page-link {
    color: var(--accent);
    background: rgba(0, 0, 0, .32);
    border-color: rgba(255, 255, 255, .12);
}

.pagination .page-link:hover,
.pagination .active .page-link {
    color: #080808;
    background: var(--accent);
    border-color: var(--accent);
}

@media (max-width: 991.98px) {
    .hero-section {
        padding: 4rem 0 3rem;
    }

    .hero-visual {
        margin-top: 2rem;
    }

    .service-selection-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767.98px) {
    .container,
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .section-padding {
        padding: 3.25rem 0;
    }

    .hero-actions .btn {
        width: 100%;
    }

    .floating-service-card,
    .floating-status-card {
        position: static;
        width: 100%;
        margin-top: .75rem;
        animation: none;
    }

    .service-selection-grid {
        grid-template-columns: 1fr;
    }

    .d-flex.justify-content-between.align-items-center,
    .d-flex.flex-wrap.justify-content-between.align-items-center {
        gap: 1rem;
    }
}
```

## `resources/views/layouts/app.blade.php`

```php
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DH Motorsport')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body class="@yield('body_class')">
@if(auth()->check())
<nav class="navbar navbar-expand-lg app-navbar sticky-top">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <span class="brand-icon"><i class="bi bi-speedometer2"></i></span>
            <span>DH Motorsport</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 nav-pills-soft">
                @if(auth()->user()->isAdmin())
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}"><i class="bi bi-people me-1"></i>Customers</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}" href="{{ route('admin.vehicles.index') }}"><i class="bi bi-car-front me-1"></i>Vehicles</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.service-packages.*') ? 'active' : '' }}" href="{{ route('admin.service-packages.index') }}"><i class="bi bi-box-seam me-1"></i>Packages</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}"><i class="bi bi-calendar-check me-1"></i>Bookings</a></li>
                @else
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('customer.vehicles.*') ? 'active' : '' }}" href="{{ route('customer.vehicles.index') }}"><i class="bi bi-car-front me-1"></i>My Vehicles</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('customer.packages.*') ? 'active' : '' }}" href="{{ route('customer.packages.index') }}"><i class="bi bi-box-seam me-1"></i>Packages</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('customer.bookings.*') ? 'active' : '' }}" href="{{ route('customer.bookings.index') }}"><i class="bi bi-calendar2-week me-1"></i>My Bookings</a></li>
                @endif
            </ul>
            <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2 gap-lg-3">
                <div class="user-chip">
                    <i class="bi bi-person-circle me-1"></i>
                    <span>{{ auth()->user()->name }}</span>
                    <span class="role-dot"></span>
                    <span>{{ ucfirst(auth()->user()->role) }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-brand btn-sm btn-rounded" type="submit"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>
@endif

<main class="@yield('main_class', 'container py-4')">
    @include('partials.flash')
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/ui.js') }}"></script>
</body>
</html>
```

## `resources/views/welcome.blade.php`

```php
@extends('layouts.app')

@section('title', 'DH Motorsport')
@section('body_class', 'landing-page dh-motorsport')
@section('main_class', 'landing-main')

@section('content')
<nav class="navbar navbar-expand-lg landing-nav">
    <div class="container py-2">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <span class="landing-brand-icon"><i class="bi bi-speedometer2"></i></span>
            <span>DH Motorsport</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavbar" aria-controls="landingNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="landingNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#how-it-works">How It Works</a></li>
                <li class="nav-item"><a class="nav-link" href="#benefits">Benefits</a></li>
                <li class="nav-item"><a class="btn btn-outline-brand btn-rounded px-3" href="{{ route('login') }}">Login</a></li>
                <li class="nav-item"><a class="btn btn-brand btn-rounded px-3" href="{{ route('register') }}">Book Service</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section">
    <div class="hero-racing-stripe" aria-hidden="true"></div>
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge mb-3"><i class="bi bi-lightning-charge-fill"></i> Motorsport-inspired service booking</div>
                <h1 class="hero-title mb-4">Book your service with <span>pit-stop confidence.</span></h1>
                <p class="hero-copy mb-4">
                    DH Motorsport helps customers register vehicles, choose a service package, select a preferred slot and track every booking from pending to completed.
                </p>
                <div class="hero-actions mb-4">
                    <a href="{{ route('register') }}" class="btn btn-brand btn-lg btn-rounded px-4"><i class="bi bi-calendar2-check me-2"></i>Start Booking</a>
                    <a href="#services" class="btn btn-outline-brand btn-lg btn-rounded px-4"><i class="bi bi-grid-3x3-gap me-2"></i>View Services</a>
                </div>
                <div class="hero-trust">
                    <span><i class="bi bi-check-circle-fill"></i> Online booking</span>
                    <span><i class="bi bi-check-circle-fill"></i> Status tracking</span>
                    <span><i class="bi bi-check-circle-fill"></i> Customer dashboard</span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-visual">
                    <div class="hero-car-card">
                        <div class="hero-card-topline">
                            <span>DH Motorsport</span>
                            <span class="race-dot"></span>
                        </div>
                        <div class="car-illustration">
                            <i class="bi bi-car-front-fill"></i>
                        </div>
                        <div class="floating-status-card"><i class="bi bi-flag-fill me-1"></i> Booking Approved</div>
                        <div class="floating-service-card">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="service-icon"><i class="bi bi-wrench-adjustable"></i></span>
                                <div>
                                    <div class="fw-bold">Performance Service</div>
                                    <div class="small text-muted">Estimated 60 minutes</div>
                                </div>
                            </div>
                            <div class="progress" role="progressbar" aria-label="Booking progress" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="services" class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge mb-3"><i class="bi bi-box-seam"></i> Service Selection</div>
            <h2 class="section-title display-6 mb-3">Choose your DH Motorsport service</h2>
            <p class="section-copy mx-auto mb-0">Click a service card to preview your choice. After logging in, the booking form keeps the same simple card-based selection.</p>
        </div>

        <div class="row g-4">
            @forelse($landingPackages as $package)
                <div class="col-lg-4 col-md-6">
                    <div class="landing-service-card" data-service-card data-service-group="landing" data-package-id="{{ $package->id }}" tabindex="0" role="button" aria-label="Select {{ $package->package_name }} service">
                        <span class="service-icon mb-3"><i class="bi bi-tools"></i></span>
                        <h3 class="h5 fw-bold mb-2">{{ $package->package_name }}</h3>
                        <p class="text-muted mb-3">{{ $package->description ?: 'A DH Motorsport service package prepared for your vehicle appointment.' }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="duration-pill"><i class="bi bi-clock me-1"></i>{{ $package->estimated_duration }} min</span>
                            <span class="price-pill">RM {{ number_format($package->price, 2) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                @foreach([
                    ['General Inspection', 'Vehicle health check, engine inspection and safety review.', '60 min', 'From RM 80'],
                    ['Oil & Filter Service', 'Engine oil replacement and filter change for smoother driving.', '45 min', 'From RM 120'],
                    ['Brake Service', 'Brake pad, fluid and system check for stronger stopping power.', '75 min', 'From RM 150'],
                ] as [$name, $desc, $duration, $price])
                    <div class="col-lg-4 col-md-6">
                        <div class="landing-service-card" data-service-card data-service-group="landing" tabindex="0" role="button" aria-label="Select {{ $name }} service">
                            <span class="service-icon mb-3"><i class="bi bi-tools"></i></span>
                            <h3 class="h5 fw-bold mb-2">{{ $name }}</h3>
                            <p class="text-muted mb-3">{{ $desc }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="duration-pill"><i class="bi bi-clock me-1"></i>{{ $duration }}</span>
                                <span class="price-pill">{{ $price }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforelse
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('register') }}" class="btn btn-brand btn-rounded px-4 py-2">Create Account to Book</a>
        </div>
    </div>
</section>

<section id="how-it-works" class="section-padding section-muted">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-lg-7">
                <div class="section-badge mb-3"><i class="bi bi-signpost-2"></i> How It Works</div>
                <h2 class="section-title display-6 mb-0">A clean booking flow from garage to finish line</h2>
            </div>
            <div class="col-lg-5">
                <p class="section-copy mb-0">The process is designed for normal customers: clear steps, obvious service choices and simple booking status updates.</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach([
                ['1', 'Create Account', 'Register as a customer and access your personal dashboard.'],
                ['2', 'Add Vehicle', 'Save your plate number, brand, model, year and color.'],
                ['3', 'Choose Service', 'Select the service package and preferred appointment time.'],
                ['4', 'Track Status', 'View whether your booking is pending, approved, completed or cancelled.'],
            ] as [$number, $title, $description])
                <div class="col-lg-3 col-md-6">
                    <div class="process-card">
                        <span class="process-number mb-3">{{ $number }}</span>
                        <h3 class="h5 fw-bold">{{ $title }}</h3>
                        <p class="text-muted mb-0">{{ $description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="benefits" class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge mb-3"><i class="bi bi-trophy"></i> Customer Benefits</div>
            <h2 class="section-title display-6 mb-3">Built for a faster service experience</h2>
            <p class="section-copy mx-auto mb-0">DH Motorsport combines a bold motorsport look with a simple system for customers and admins to manage service appointments clearly.</p>
        </div>

        <div class="row g-4">
            @foreach([
                ['bi-phone', 'Mobile Friendly', 'Book or check your appointment from desktop, tablet or phone.'],
                ['bi-shield-check', 'Secure Access', 'Your vehicles and bookings are protected inside your own account.'],
                ['bi-search', 'Easy Tracking', 'Quickly view booking details, service price and current status.'],
                ['bi-receipt', 'Clear Records', 'Every booking stores date, time, service type and total price.'],
                ['bi-lightning-charge', 'Fast Actions', 'Edit or cancel pending bookings before approval.'],
                ['bi-flag', 'Sporty Experience', 'Red, yellow and black visuals make the interface feel energetic.'],
            ] as [$icon, $title, $description])
                <div class="col-lg-4 col-md-6">
                    <div class="benefit-card">
                        <span class="benefit-icon mb-3"><i class="bi {{ $icon }}"></i></span>
                        <h3 class="h5 fw-bold">{{ $title }}</h3>
                        <p class="text-muted mb-0">{{ $description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section-padding pt-0">
    <div class="container">
        <div class="cta-panel">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2 class="display-6 fw-bold mb-3">Ready for your next DH Motorsport service?</h2>
                    <p class="mb-0 opacity-75">Create an account, add your vehicle and submit your preferred appointment in a few simple steps.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg btn-rounded px-4"><i class="bi bi-arrow-right-circle me-2"></i>Get Started</a>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="landing-footer py-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="landing-brand-icon"><i class="bi bi-speedometer2"></i></span>
            <div>
                <div class="fw-bold">DH Motorsport</div>
                <div class="small text-muted">Online booking, vehicle management and service tracking.</div>
            </div>
        </div>
        <div class="d-flex gap-3 small">
            <a href="#services" class="footer-link">Services</a>
            <a href="#how-it-works" class="footer-link">Process</a>
            <a href="{{ route('login') }}" class="footer-link">Login</a>
        </div>
    </div>
</footer>
@endsection
```

## `resources/views/auth/login.blade.php`

```php
@extends('layouts.app')

@section('title', 'Login | DH Motorsport')

@section('content')
<div class="row justify-content-center auth-shell">
    <div class="col-lg-5 col-md-7">
        <div class="card p-4">
            <div class="text-center mb-4">
                <div class="display-5 auth-brand-mark"><i class="bi bi-speedometer2"></i></div>
                <h1 class="h3 fw-bold">DH Motorsport</h1>
                <p class="text-muted mb-0">Login to manage your motorsport service bookings.</p>
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
                <button class="btn btn-brand w-100" type="submit">Login</button>
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

@section('title', 'Register | DH Motorsport')

@section('content')
<div class="row justify-content-center auth-shell">
    <div class="col-lg-6 col-md-8">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h1 class="h3 fw-bold">Create DH Motorsport Account</h1>
                <p class="text-muted mb-0">Register to add your vehicle and book a DH Motorsport service slot.</p>
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
                <button class="btn btn-brand w-100" type="submit">Register</button>
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
@php
    $selectedPackageId = old('service_package_id', $booking->service_package_id ?? request('service_package_id'));
@endphp

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

    <div class="col-12 mb-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mb-3">
            <div>
                <label class="form-label mb-1">Service Package</label>
                <p class="text-muted small mb-0">Click a service card or use the dropdown below. The selected service will be submitted as the booking package.</p>
            </div>
            <span class="section-badge align-self-lg-start"><i class="bi bi-hand-index-thumb"></i> Select one service</span>
        </div>

        <div class="service-selection-grid mb-3">
            @forelse($packages as $package)
                <div class="service-option-card {{ (string) $selectedPackageId === (string) $package->id ? 'selected' : '' }}"
                     data-service-card
                     data-service-group="booking-form"
                     data-package-id="{{ $package->id }}"
                     data-target-select="service_package_id"
                     tabindex="0"
                     role="button"
                     aria-label="Select {{ $package->package_name }}">
                    <input type="radio" aria-hidden="true" @checked((string) $selectedPackageId === (string) $package->id)>
                    <span class="service-icon mb-3"><i class="bi bi-tools"></i></span>
                    <h3 class="h6 service-option-title mb-2">{{ $package->package_name }}</h3>
                    <p class="service-option-desc mb-0">{{ $package->description ?: 'DH Motorsport service package for your vehicle appointment.' }}</p>
                    <div class="service-option-meta">
                        <span class="duration-pill"><i class="bi bi-clock me-1"></i>{{ $package->estimated_duration }} min</span>
                        <span class="price-pill">RM {{ number_format($package->price, 2) }}</span>
                    </div>
                </div>
            @empty
                <div class="alert alert-info mb-0">No active service packages are available.</div>
            @endforelse
        </div>

        <label class="form-label small text-muted">Selected Service Package</label>
        <select id="service_package_id" name="service_package_id" class="form-select service-package-select" required>
            <option value="">-- Select service package --</option>
            @foreach($packages as $package)
                <option value="{{ $package->id }}" @selected((string) $selectedPackageId === (string) $package->id)>
                    {{ $package->package_name }} - RM {{ number_format($package->price, 2) }}
                </option>
            @endforeach
        </select>
        <div class="selected-service-helper alert alert-success mt-2 mb-0 py-2" data-selected-helper="service_package_id"></div>
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

## `resources/views/customer/packages/index.blade.php`

```php
@extends('layouts.app')
@section('title', 'Service Packages')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <span class="section-badge mb-2"><i class="bi bi-box-seam"></i> Available Services</span>
        <h1 class="page-title mb-1">Choose Your Service Package</h1>
        <p class="text-muted mb-0">Review DH Motorsport service packages and continue to booking when you are ready.</p>
    </div>
    <a href="{{ route('customer.bookings.create') }}" class="btn btn-brand btn-rounded px-4"><i class="bi bi-calendar2-plus me-1"></i>Book Now</a>
</div>

<div class="row g-4">
@forelse($packages as $package)
    <div class="col-lg-4 col-md-6">
        <div class="landing-service-card h-100" data-service-card data-service-group="customer-packages" data-package-id="{{ $package->id }}" tabindex="0" role="button" aria-label="Select {{ $package->package_name }}">
            <span class="service-icon mb-3"><i class="bi bi-tools"></i></span>
            <h2 class="h5 fw-bold mb-2">{{ $package->package_name }}</h2>
            <p class="text-muted">{{ $package->description ?: 'DH Motorsport service package for your vehicle appointment.' }}</p>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="duration-pill"><i class="bi bi-clock me-1"></i>{{ $package->estimated_duration }} minutes</span>
                <span class="price-pill">RM {{ number_format($package->price, 2) }}</span>
            </div>
            <a href="{{ route('customer.bookings.create', ['service_package_id' => $package->id]) }}" class="btn btn-outline-brand btn-rounded w-100 mt-4">
                Select & Book This Service
            </a>
        </div>
    </div>
@empty
    <div class="col-12"><div class="alert alert-info">No active packages available.</div></div>
@endforelse
</div>
<div class="mt-3">{{ $packages->links() }}</div>
@endsection
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
    <h1>DH Motorsport Booking Report</h1>
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

