<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public const STATUSES = ['Pending', 'Approved', 'Rejected', 'Completed', 'Cancelled'];

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'service_package_id',
        'workshop_id',
        'preferred_date',
        'preferred_time',
        'additional_notes',
        'status',
        'total_price',
        'admin_remarks',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function servicePackage()
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->payment && $this->payment->status === 'Paid';
    }

    public function statusLogs()
    {
        return $this->hasMany(BookingStatusLog::class)->latest();
    }

    public function canBeEditedByCustomer(): bool
    {
        return $this->status === 'Pending';
    }

    public function canBeCancelledByCustomer(): bool
    {
        return in_array($this->status, ['Pending', 'Approved'], true);
    }
}
