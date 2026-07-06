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
