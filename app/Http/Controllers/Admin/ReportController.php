<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function exportBookingsPdf(Request $request)
    {
        $bookings = BookingController::filteredBookingQuery($request)
            ->orderByDesc('preferred_date')
            ->orderByDesc('preferred_time')
            ->get();

        $pdf = Pdf::loadView('pdf.bookings', [
            'bookings' => $bookings,
            'filters' => $request->only(['customer', 'plate_number', 'service_type', 'preferred_date', 'status']),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('booking-report-' . now()->format('Ymd-His') . '.pdf');
    }
}
