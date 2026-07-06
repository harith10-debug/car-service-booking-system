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
