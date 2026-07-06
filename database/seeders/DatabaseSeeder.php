<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0123456789',
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Demo Customer',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '0112233445',
            ]
        );

        $packages = [
            [
                'package_name' => 'Basic Service',
                'description' => 'Engine oil replacement, oil filter replacement, 20-point vehicle inspection.',
                'estimated_duration' => 60,
                'price' => 150.00,
                'status' => 'Active',
            ],
            [
                'package_name' => 'Full Service',
                'description' => 'Basic service plus air filter, brake inspection, coolant and battery inspection.',
                'estimated_duration' => 120,
                'price' => 320.00,
                'status' => 'Active',
            ],
            [
                'package_name' => 'Major Service',
                'description' => 'Full service plus spark plugs, transmission check, tyre rotation and diagnostic scan.',
                'estimated_duration' => 180,
                'price' => 580.00,
                'status' => 'Active',
            ],
            [
                'package_name' => 'Aircond Service',
                'description' => 'Aircond gas check, blower cleaning and cooling performance test.',
                'estimated_duration' => 90,
                'price' => 220.00,
                'status' => 'Active',
            ],
        ];

        foreach ($packages as $package) {
            ServicePackage::updateOrCreate(['package_name' => $package['package_name']], $package);
        }

        $vehicle = Vehicle::updateOrCreate(
            ['user_id' => $customer->id, 'plate_number' => 'ABC1234'],
            [
                'brand' => 'Perodua',
                'model' => 'Myvi',
                'year' => 2021,
                'color' => 'Silver',
            ]
        );

        $package = ServicePackage::where('package_name', 'Basic Service')->first();

        $booking = Booking::firstOrCreate(
            [
                'user_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
                'service_package_id' => $package->id,
                'preferred_date' => now()->addDays(3)->toDateString(),
                'preferred_time' => '10:00',
            ],
            [
                'additional_notes' => 'Please check engine sound.',
                'status' => 'Pending',
                'total_price' => $package->price,
            ]
        );

        BookingStatusLog::firstOrCreate(
            ['booking_id' => $booking->id, 'to_status' => 'Pending'],
            ['changed_by' => $customer->id, 'remarks' => 'Sample booking created by seeder.']
        );
    }
}
