<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; }
        h1 { color: #e10600; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f3f3f3; }
        .summary { padding: 10px; border: 1px solid #ddd; background: #fafafa; margin: 12px 0; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>DH Motorsport Payment & Sales Report</h1>
    <div>Generated: {{ $generatedAt->format('d M Y h:i A') }}</div>
    <div class="summary">
        <strong>Total Paid Sales:</strong> RM {{ number_format($totalSales, 2) }}<br>
        <strong>Filters:</strong>
        @forelse($filters as $key => $value)
            @if($value) {{ Str::headline($key) }}: {{ $value }}; @endif
        @empty
            None
        @endforelse
    </div>
    <table>
        <thead>
            <tr><th>Reference</th><th>Customer</th><th>Booking</th><th>Vehicle</th><th>Method</th><th>Status</th><th>Paid At</th><th class="right">Total Paid</th></tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_reference }}</td>
                    <td>{{ $payment->user->name }}</td>
                    <td>#{{ $payment->booking_id }} - {{ $payment->booking->servicePackage->package_name ?? '-' }}</td>
                    <td>{{ $payment->booking->vehicle->plate_number ?? '-' }}</td>
                    <td>{{ $payment->method }}</td>
                    <td>{{ $payment->status }}</td>
                    <td>{{ $payment->paid_at?->format('d M Y') ?? '-' }}</td>
                    <td class="right">RM {{ number_format($payment->total_paid, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;">No payment records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
