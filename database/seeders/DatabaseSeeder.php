<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\Payment;
use App\Models\ServicePackage;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Vehicle;
use App\Models\Workshop;
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

        $workshopData = [
            [
                'name' => 'DH Motorsport Shah Alam HQ',
                'address' => 'No. 12, Jalan Penguasa U1/53, Temasya Industrial Park',
                'city' => 'Shah Alam',
                'state' => 'Selangor',
                'postcode' => '40150',
                'phone' => '03-5566 7788',
                'email' => 'shahalam@dhmotorsport.test',
                'latitude' => 3.0738000,
                'longitude' => 101.5183000,
                'services' => 'General service, diagnostic scan, tyres, aircond, brake inspection',
                'opening_hours' => 'Mon-Sat, 9.00 AM - 6.00 PM',
                'maps_url' => 'https://maps.google.com/?q=Shah+Alam+Temasya',
                'status' => 'Active',
            ],
            [
                'name' => 'DH Motorsport Seksyen 7',
                'address' => 'Lot 7, Jalan Plumbum 7/102, Seksyen 7',
                'city' => 'Shah Alam',
                'state' => 'Selangor',
                'postcode' => '40000',
                'phone' => '03-5522 1188',
                'email' => 'seksyen7@dhmotorsport.test',
                'latitude' => 3.0902000,
                'longitude' => 101.4925000,
                'services' => 'Oil service, brake service, battery check, alignment',
                'opening_hours' => 'Mon-Sun, 10.00 AM - 7.00 PM',
                'maps_url' => 'https://maps.google.com/?q=Shah+Alam+Seksyen+7',
                'status' => 'Active',
            ],
            [
                'name' => 'DH Motorsport Subang Jaya',
                'address' => '25, Jalan SS15/4, Subang Jaya',
                'city' => 'Subang Jaya',
                'state' => 'Selangor',
                'postcode' => '47500',
                'phone' => '03-5633 2211',
                'email' => 'subang@dhmotorsport.test',
                'latitude' => 3.0744000,
                'longitude' => 101.5889000,
                'services' => 'Diagnostic scan, aircond service, major service, suspension check',
                'opening_hours' => 'Mon-Sat, 9.30 AM - 6.30 PM',
                'maps_url' => 'https://maps.google.com/?q=Subang+Jaya+SS15',
                'status' => 'Active',
            ],
            [
                'name' => 'DH Motorsport Klang',
                'address' => '8, Jalan Batu Tiga Lama, Kawasan 16',
                'city' => 'Klang',
                'state' => 'Selangor',
                'postcode' => '41300',
                'phone' => '03-3344 9011',
                'email' => 'klang@dhmotorsport.test',
                'latitude' => 3.0449000,
                'longitude' => 101.4456000,
                'services' => 'Tyre rotation, oil service, brake inspection, coolant service',
                'opening_hours' => 'Tue-Sun, 9.00 AM - 6.00 PM',
                'maps_url' => 'https://maps.google.com/?q=Klang+Selangor',
                'status' => 'Active',
            ],
        ];

        foreach ($workshopData as $workshop) {
            Workshop::updateOrCreate(['name' => $workshop['name']], $workshop);
        }

        $plans = [
            [
                'plan_name' => 'Silver Care',
                'description' => 'Starter membership for regular service customers.',
                'monthly_price' => 19.90,
                'billing_cycle' => 'Monthly',
                'discount_percentage' => 5,
                'priority_level' => 2,
                'benefits' => "5% discount on service payment\nPriority booking tag\nDigital receipt history",
                'status' => 'Active',
            ],
            [
                'plan_name' => 'Gold Performance',
                'description' => 'Best value for customers who service vehicles frequently.',
                'monthly_price' => 39.90,
                'billing_cycle' => 'Monthly',
                'discount_percentage' => 10,
                'priority_level' => 4,
                'benefits' => "10% discount on service payment\nPriority booking queue\nFree diagnostic note\nWorkshop recommendation",
                'status' => 'Active',
            ],
            [
                'plan_name' => 'Platinum Motorsport',
                'description' => 'Premium plan with highest priority and strongest service benefits.',
                'monthly_price' => 399.00,
                'billing_cycle' => 'Yearly',
                'discount_percentage' => 15,
                'priority_level' => 6,
                'benefits' => "15% discount on service payment\nTop priority booking\nFree yearly inspection reminder\nAdmin VIP tag",
                'status' => 'Active',
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['plan_name' => $plan['plan_name']], $plan);
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

        $basicPackage = ServicePackage::where('package_name', 'Basic Service')->first();
        $fullPackage = ServicePackage::where('package_name', 'Full Service')->first();
        $mainWorkshop = Workshop::where('name', 'DH Motorsport Shah Alam HQ')->first();
        $goldPlan = SubscriptionPlan::where('plan_name', 'Gold Performance')->first();

        UserSubscription::updateOrCreate(
            ['subscription_reference' => 'SUB-DEMO-GOLD'],
            [
                'user_id' => $customer->id,
                'subscription_plan_id' => $goldPlan->id,
                'starts_at' => now()->subDays(5),
                'ends_at' => now()->addMonth(),
                'status' => 'Active',
                'amount_paid' => $goldPlan->monthly_price,
                'payment_method' => 'Online Banking',
                'auto_renew' => true,
            ]
        );

        $pendingBooking = Booking::firstOrCreate(
            [
                'user_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
                'service_package_id' => $basicPackage->id,
                'preferred_date' => now()->addDays(3)->toDateString(),
                'preferred_time' => '10:00',
            ],
            [
                'workshop_id' => $mainWorkshop->id,
                'additional_notes' => 'Please check engine sound.',
                'status' => 'Pending',
                'total_price' => $basicPackage->price,
            ]
        );

        if (! $pendingBooking->workshop_id) {
            $pendingBooking->update(['workshop_id' => $mainWorkshop->id]);
        }

        BookingStatusLog::firstOrCreate(
            ['booking_id' => $pendingBooking->id, 'to_status' => 'Pending'],
            ['changed_by' => $customer->id, 'remarks' => 'Sample booking created by seeder.']
        );

        $paidBooking = Booking::updateOrCreate(
            [
                'user_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
                'service_package_id' => $fullPackage->id,
                'preferred_date' => now()->addDays(1)->toDateString(),
                'preferred_time' => '14:00',
            ],
            [
                'workshop_id' => $mainWorkshop->id,
                'additional_notes' => 'Demo paid booking for sales monitor.',
                'status' => 'Approved',
                'total_price' => $fullPackage->price,
                'admin_remarks' => 'Booking approved by admin.',
            ]
        );

        BookingStatusLog::firstOrCreate(
            ['booking_id' => $paidBooking->id, 'to_status' => 'Approved'],
            ['changed_by' => $admin->id, 'from_status' => 'Pending', 'remarks' => 'Demo booking approved by admin.']
        );

        Payment::updateOrCreate(
            ['payment_reference' => 'PAY-DEMO-0001'],
            [
                'booking_id' => $paidBooking->id,
                'user_id' => $customer->id,
                'method' => 'Online Banking',
                'amount' => $fullPackage->price,
                'discount_amount' => round($fullPackage->price * 0.10, 2),
                'total_paid' => round($fullPackage->price * 0.90, 2),
                'status' => 'Paid',
                'paid_at' => now()->subHours(2),
                'payer_name' => $customer->name,
                'payer_email' => $customer->email,
                'transaction_note' => 'Seeder demo payment with Gold Performance discount.',
            ]
        );
    }
}
