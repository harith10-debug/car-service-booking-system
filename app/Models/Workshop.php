<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'postcode',
        'phone',
        'email',
        'latitude',
        'longitude',
        'services',
        'opening_hours',
        'maps_url',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
