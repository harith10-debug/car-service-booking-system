# Entity Relationship Diagram Description

```mermaid
erDiagram
    USERS ||--o{ VEHICLES : owns
    USERS ||--o{ BOOKINGS : makes
    USERS ||--o{ PAYMENTS : pays
    USERS ||--o{ USER_SUBSCRIPTIONS : subscribes
    USERS ||--o{ BOOKING_STATUS_LOGS : changes
    VEHICLES ||--o{ BOOKINGS : used_for
    SERVICE_PACKAGES ||--o{ BOOKINGS : selected_in
    WORKSHOPS ||--o{ BOOKINGS : assigned_to
    BOOKINGS ||--o{ BOOKING_STATUS_LOGS : has
    BOOKINGS ||--o| PAYMENTS : has
    SUBSCRIPTION_PLANS ||--o{ USER_SUBSCRIPTIONS : includes

    USERS {
        bigint id PK
        string name
        string email UK
        enum role
        string phone
    }
    VEHICLES {
        bigint id PK
        bigint user_id FK
        string plate_number
        string brand
        string model
        int year
    }
    SERVICE_PACKAGES {
        bigint id PK
        string package_name
        int estimated_duration
        decimal price
        enum status
    }
    WORKSHOPS {
        bigint id PK
        string name
        string address
        string city
        decimal latitude
        decimal longitude
        enum status
    }
    BOOKINGS {
        bigint id PK
        bigint user_id FK
        bigint vehicle_id FK
        bigint service_package_id FK
        bigint workshop_id FK
        date preferred_date
        time preferred_time
        enum status
        decimal total_price
    }
    PAYMENTS {
        bigint id PK
        bigint booking_id FK
        bigint user_id FK
        string payment_reference UK
        enum method
        decimal amount
        decimal discount_amount
        decimal total_paid
        enum status
    }
    SUBSCRIPTION_PLANS {
        bigint id PK
        string plan_name UK
        decimal monthly_price
        decimal discount_percentage
        int priority_level
        enum status
    }
    USER_SUBSCRIPTIONS {
        bigint id PK
        bigint user_id FK
        bigint subscription_plan_id FK
        string subscription_reference UK
        timestamp starts_at
        timestamp ends_at
        enum status
    }
```

## Normalisation Notes

- User data is stored once in `users` and referenced by vehicles, bookings, payments and subscriptions.
- Vehicle records are separated from bookings to prevent repeating plate, brand and model in every booking.
- Service package price and duration are stored in `service_packages`; booking stores `total_price` as a transaction snapshot.
- Workshop location is separated into `workshops` so customers can search nearby locations and bookings can reference one workshop.
- Payment is separated from booking because not all bookings are paid immediately.
- Subscription plans are separated from user subscriptions so admin can manage reusable plan definitions.
