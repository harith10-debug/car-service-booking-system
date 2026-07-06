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
