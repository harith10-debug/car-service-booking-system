<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    public function index(Request $request)
    {
        $originLat = $request->filled('lat') ? (float) $request->lat : 3.0738; // Shah Alam centre
        $originLng = $request->filled('lng') ? (float) $request->lng : 101.5183;

        $workshops = Workshop::where('status', 'Active')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('services', 'like', "%{$search}%");
                });
            })
            ->orderBy('city')
            ->orderBy('name')
            ->get()
            ->map(function (Workshop $workshop) use ($originLat, $originLng) {
                $workshop->distance_km = $this->distanceKm($originLat, $originLng, (float) $workshop->latitude, (float) $workshop->longitude);
                return $workshop;
            })
            ->sortBy('distance_km')
            ->values();

        return view('customer.workshops.index', compact('workshops', 'originLat', 'originLng'));
    }

    private function distanceKm(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        if (! $toLat || ! $toLng) {
            return 999.0;
        }

        $earthRadius = 6371;
        $latDelta = deg2rad($toLat - $fromLat);
        $lngDelta = deg2rad($toLng - $fromLng);
        $a = sin($latDelta / 2) ** 2 + cos(deg2rad($fromLat)) * cos(deg2rad($toLat)) * sin($lngDelta / 2) ** 2;

        return round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)), 1);
    }
}
