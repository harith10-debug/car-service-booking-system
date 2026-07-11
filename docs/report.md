# DH Motorsport Car Service Booking System Report

## 1. Introduction

DH Motorsport Car Service Booking System is a Laravel web application that digitalises the car service booking process. Customers can register, add vehicles, select service packages, choose nearby workshops, book appointments, make payment and download receipts. Admin can monitor customers, vehicles, service packages, workshops, booking approvals, payments, sales and subscriptions.

## 2. GitHub Repository Link

Add the final GitHub repository link here before submission.

## 3. Entity Relationship Diagram

Refer to `docs/erd.md` for the Mermaid ERD and relationship explanation.

## 4. System Requirements

- PHP 8.1 or above
- Laravel 10
- MySQL
- Composer
- Node.js and NPM
- Google Chrome browser
- DomPDF package for PDF export and receipt generation

## 5. Installation and Setup Guide

1. Extract the project.
2. Run `composer install`.
3. Run `npm install`.
4. Copy `.env.example` to `.env`.
5. Create database `car_service_booking`.
6. Update database credentials in `.env`.
7. Run `php artisan key:generate`.
8. Run `php artisan migrate --seed`.
9. Run `php artisan serve`.
10. Open `http://127.0.0.1:8000` in Google Chrome.

## 6. Features and Functionalities

### Customer

- Register/login/logout.
- Vehicle CRUD.
- Booking CRUD with service package card selection.
- Nearby workshop finder with search and browser location sorting.
- Preferred workshop selection during booking.
- Payment for approved/completed bookings.
- PDF receipt download.
- Payment history.
- Subscription plan page with benefits for customer and admin.
- Subscription discount applied during payment.

### Admin

- Admin dashboard with totals, booking queue, recent payments and sales metrics.
- Accept/reject/complete booking monitor.
- Booking search/filter and PDF export.
- Payment and sale monitor with method breakdown and PDF export.
- Service package CRUD.
- Workshop CRUD.
- Subscription plan CRUD.
- Subscription monitor for active subscribers and revenue.
- Customer and vehicle monitoring.

## 7. User Interface Overview

The UI uses Bootstrap 5 and custom CSS. Navigation is separated by role. Customer navigation includes Dashboard, My Vehicles, Packages, Workshops, My Bookings, Payments and Subscription. Admin navigation includes Dashboard, Customers, Vehicles, Workshops, Packages, Bookings, Sales and Subscriptions.

## 8. Workflow of Form

1. Customer registers/logs in.
2. Customer creates vehicle record.
3. Customer selects service package and workshop.
4. Customer submits booking.
5. Admin reviews booking in the acceptance monitor.
6. Admin accepts/rejects/completes booking.
7. Customer pays approved/completed booking.
8. System stores payment and generates receipt.
9. Admin monitors payment and sales.

## 9. Team Roles and Contributions

Replace this section with actual group member names and roles.

- Project Manager: coordinates tasks and presentation.
- Backend Developer: Laravel controllers, routes, models and migrations.
- Frontend Developer: Blade views, Bootstrap UI and JavaScript interactions.
- Database Designer: ERD, migrations and seed data.
- Documentation Lead: report, user manual and test cases.

## 10. Contact Information

Support email: support@dhmotorsport.test

## 11. Conclusion and Reflection

The system meets the IMS566 requirements by using a backend framework, authentication, database interaction, CRUD operations, search/filter, PDF export and responsive UI. Extra features such as payment, receipt, nearby workshop finder, sales monitor and subscription plans improve system usefulness and demonstrate stronger technical implementation.
