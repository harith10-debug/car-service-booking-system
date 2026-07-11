@php
    $paymentStatus = $payment?->status ?? 'Unpaid';
    $paymentClasses = [
        'Paid' => 'bg-success',
        'Pending' => 'bg-warning text-dark',
        'Failed' => 'bg-danger',
        'Refunded' => 'bg-info text-dark',
        'Unpaid' => 'bg-secondary',
    ];
@endphp
<span class="badge {{ $paymentClasses[$paymentStatus] ?? 'bg-secondary' }}">{{ $paymentStatus }}</span>
