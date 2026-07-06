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
    <h1>Car Service Booking Report</h1>
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
