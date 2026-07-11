# DH Motorsport Test Cases

| ID | Module | Test Scenario | Expected Result |
|---|---|---|---|
| TC01 | Authentication | Customer logs in with correct credentials | Redirects to customer dashboard |
| TC02 | Authentication | Admin logs in with correct credentials | Redirects to admin dashboard |
| TC03 | Booking CRUD | Customer creates booking with vehicle, service and workshop | Booking is saved as Pending |
| TC04 | Booking CRUD | Customer edits a Pending booking | Booking details update successfully |
| TC05 | Booking CRUD | Customer cancels Pending/Approved booking | Booking status changes to Cancelled |
| TC06 | Admin Booking | Admin accepts Pending booking | Booking status changes to Approved and log is created |
| TC07 | Payment | Customer tries to pay Pending booking | System blocks payment until approval |
| TC08 | Payment | Customer pays Approved booking | Payment is saved as Paid |
| TC09 | Receipt | Customer downloads receipt PDF | PDF receipt is generated |
| TC10 | Workshop Finder | Customer searches Shah Alam workshop | Matching workshops display sorted by distance |
| TC11 | Admin Sales | Admin filters payment by method/status/date | Payment list updates based on filters |
| TC12 | PDF Export | Admin exports sales PDF | PDF report downloads successfully |
| TC13 | Subscription | Customer subscribes to Gold Performance | Active subscription is saved |
| TC14 | Subscription Discount | Customer with active plan pays booking | Discount is applied in payment amount |
| TC15 | Admin Plan CRUD | Admin creates/updates subscription plan | Plan is stored and visible to customers if Active |
