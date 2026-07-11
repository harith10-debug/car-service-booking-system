<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    public const STATUSES = ['Active', 'Expired', 'Cancelled'];

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'subscription_reference',
        'starts_at',
        'ends_at',
        'status',
        'amount_paid',
        'payment_method',
        'auto_renew',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'auto_renew' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'Active' && $this->starts_at <= now() && $this->ends_at >= now();
    }
}
