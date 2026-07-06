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
