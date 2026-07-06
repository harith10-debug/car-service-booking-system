@php
    $classes = [
        'Pending' => 'bg-warning text-dark',
        'Approved' => 'bg-primary',
        'Rejected' => 'bg-danger',
        'Completed' => 'bg-success',
        'Cancelled' => 'bg-secondary',
    ];
@endphp
<span class="badge badge-status {{ $classes[$status] ?? 'bg-dark' }}">{{ $status }}</span>
