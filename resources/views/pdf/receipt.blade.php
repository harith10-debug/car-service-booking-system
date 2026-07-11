<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $payment->payment_reference }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #222; font-size: 12px; }
        .header { border-bottom: 3px solid #e10600; padding-bottom: 12px; margin-bottom: 18px; }
        .brand { font-size: 24px; font-weight: bold; color: #e10600; }
        .muted { color: #666; }
        .badge { display: inline-block; padding: 6px 10px; background: #198754; color: #fff; border-radius: 6px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .total { font-size: 18px; font-weight: bold; color: #198754; }
        .right { text-align: right; }
        .footer { margin-top: 24px; font-size: 11px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">DH Motorsport</div>
        <div class="muted">Official Payment Receipt</div>
        <div class="right"><span class="badge">{{ $payment->status }}</span></div>
    </div>

    <table>
        <tr><th>Receipt Reference</th><td>{{ $payment->payment_reference }}</td></tr>
        <tr><th>Generated At</th><td>{{ $generatedAt->format('d M Y h:i A') }}</td></tr>
        <tr><th>Paid At</th><td>{{ $payment->paid_at?->format('d M Y h:i A') }}</td></tr>
        <tr><th>Customer</th><td>{{ $payment->user->name }} ({{ $payment->user->email }})</td></tr>
        <tr><th>Payer</th><td>{{ $payment->payer_name }} {{ $payment->payer_email ? '(' . $payment->payer_email . ')' : '' }}</td></tr>
        <tr><th>Payment Method</th><td>{{ $payment->method }} @if($payment->card_last_four) •••• {{ $payment->card_last_four }} @endif</td></tr>
    </table>

    <table>
        <thead>
            <tr><th>Booking</th><th>Vehicle</th><th>Service</th><th>Workshop</th><th class="right">Amount</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>#{{ $payment->booking_id }}</td>
                <td>{{ $payment->booking->vehicle->plate_number }} - {{ $payment->booking->vehicle->brand }} {{ $payment->booking->vehicle->model }}</td>
                <td>{{ $payment->booking->servicePackage->package_name }}</td>
                <td>{{ $payment->booking->workshop->name ?? 'Admin assigned' }}</td>
                <td class="right">RM {{ number_format($payment->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table>
        <tr><th class="right">Service Amount</th><td class="right">RM {{ number_format($payment->amount, 2) }}</td></tr>
        <tr><th class="right">Subscription Discount</th><td class="right">RM {{ number_format($payment->discount_amount, 2) }}</td></tr>
        <tr><th class="right total">Total Paid</th><td class="right total">RM {{ number_format($payment->total_paid, 2) }}</td></tr>
    </table>

    <p><strong>Transaction Note:</strong> {{ $payment->transaction_note ?: '-' }}</p>

    <div class="footer">
        This receipt was generated from the DH Motorsport Car Service Booking System database. Please keep it for your records.
    </div>
</body>
</html>
