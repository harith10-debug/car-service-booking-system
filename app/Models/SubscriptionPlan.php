<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    public const BILLING_CYCLES = ['Monthly', 'Yearly'];
    public const STATUSES = ['Active', 'Inactive'];

    protected $fillable = [
        'plan_name',
        'description',
        'monthly_price',
        'billing_cycle',
        'discount_percentage',
        'priority_level',
        'benefits',
        'status',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
    ];

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }
}
