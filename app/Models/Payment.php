<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public const METHODS = ['Cash', 'Card', 'Online Banking', 'E-Wallet'];
    public const STATUSES = ['Pending', 'Paid', 'Failed', 'Refunded'];

    protected $fillable = [
        'booking_id',
        'user_id',
        'payment_reference',
        'method',
        'amount',
        'discount_amount',
        'total_paid',
        'status',
        'paid_at',
        'payer_name',
        'payer_email',
        'card_last_four',
        'transaction_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'Paid';
    }
}
