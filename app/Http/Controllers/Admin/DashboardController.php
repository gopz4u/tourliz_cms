<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomItinerary;
use App\Models\B2CItinerary;
use App\Models\Destination;
use App\Models\Package;
use App\Models\Service;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'places' => Destination::count(),
            'packages' => Package::count(),
            'services' => Service::count(),
            'bookings' => \App\Models\Booking::count(),
            'b2b_leads' => CustomItinerary::count(),
            'b2c_leads' => B2CItinerary::count(),
            'pending_reviews' => \App\Models\Review::where('status', 'pending')->count(),
        ];

        // --- Advanced Sales Analytics ---
        $bookingRevenue = \App\Models\Booking::sum(\DB::raw('COALESCE(total_amount, price, 0)'));
        $b2bRevenue = CustomItinerary::sum('total_price');
        $b2cRevenue = B2CItinerary::sum('total_price');
        $totalRevenue = $bookingRevenue + $b2bRevenue + $b2cRevenue;

        $bookingReceived = \App\Models\Booking::where('payment_status', 'paid')->sum(\DB::raw('COALESCE(total_amount, price, 0)')); // Simplified
        $b2bReceived = CustomItinerary::sum('total_amount_received');
        $b2cReceived = B2CItinerary::sum('total_amount_received');
        $totalReceived = $bookingReceived + $b2bReceived + $b2cReceived;

        // Only sum expenses of active (non-soft-deleted) B2B/B2C itineraries
        $b2bExpenses = \App\Models\ItineraryExpense::where('itinerary_type', 'b2b')
            ->whereIn('itinerary_id', CustomItinerary::pluck('id'))
            ->sum('amount');
        $b2cExpenses = \App\Models\ItineraryExpense::where('itinerary_type', 'b2c')
            ->whereIn('itinerary_id', B2CItinerary::pluck('id'))
            ->sum('amount');
        $totalExpenses = $b2bExpenses + $b2cExpenses;
        $netProfit = $totalRevenue - $totalExpenses;

        $salesStats = [
            'quoted' => number_format($totalRevenue, 2),
            'received' => number_format($totalReceived, 2),
            'expenses' => number_format($totalExpenses, 2),
            'profit' => number_format($netProfit, 2),
            'conversion_rate' => $this->getConversionRate(),
        ];

        // --- Monthly Revenue Data (Last 6 Months) ---
        $chartData = $this->getMonthlyRevenueData();

        $today = Carbon::today();
        $threeDaysFromNow = Carbon::today()->addDays(3);

        // --- Reminders: Arrival in 3 Days ---
        $bookingArrivals = \App\Models\Booking::where('travel_date', $threeDaysFromNow)
            ->with(['package'])
            ->get();

        $b2bArrivals = CustomItinerary::where('start_date', $threeDaysFromNow)
            ->with(['agency', 'destination'])
            ->get();

        $b2cArrivals = B2CItinerary::where('start_date', $threeDaysFromNow)
            ->with(['destination'])
            ->get();

        // --- Followup Notifications (Home Page) ---
        $bookingFollowups = \App\Models\Booking::whereNotNull('next_followup_date')
            ->where('next_followup_date', '<=', $today)
            ->whereNotIn('followup_status', ['converted', 'dead'])
            ->get();

        $b2bFollowups = CustomItinerary::whereNotNull('next_followup_date')
            ->where('next_followup_date', '<=', $today)
            ->whereNotIn('followup_status', ['converted', 'dead'])
            ->with(['agency'])
            ->get();

        $b2Followups = B2CItinerary::whereNotNull('next_followup_date')
            ->where('next_followup_date', '<=', $today)
            ->whereNotIn('followup_status', ['converted', 'dead'])
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'salesStats',
            'chartData',
            'bookingArrivals',
            'b2bArrivals',
            'b2cArrivals',
            'bookingFollowups',
            'b2bFollowups',
            'b2Followups'
        ));
    }

    private function getConversionRate()
    {
        $total = \App\Models\Booking::count() + CustomItinerary::count() + B2CItinerary::count();
        if ($total == 0)
            return 0;

        $converted = \App\Models\Booking::where('followup_status', 'converted')->count() +
            CustomItinerary::where('followup_status', 'converted')->count() +
            B2CItinerary::where('followup_status', 'converted')->count();

        return number_format(($converted / $total) * 100, 1);
    }

    private function getMonthlyRevenueData()
    {
        $labels = [];
        $revenue = [];
        $profit = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $mB2B = CustomItinerary::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_price');

            $mB2C = B2CItinerary::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_price');

            $mBooking = \App\Models\Booking::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum(\DB::raw('COALESCE(total_amount, price, 0)'));

            // Only sum expenses of active (non-soft-deleted) B2B/B2C itineraries
            $mExpB2B = \App\Models\ItineraryExpense::where('itinerary_type', 'b2b')
                ->whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->whereIn('itinerary_id', CustomItinerary::pluck('id'))
                ->sum('amount');

            $mExpB2C = \App\Models\ItineraryExpense::where('itinerary_type', 'b2c')
                ->whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->whereIn('itinerary_id', B2CItinerary::pluck('id'))
                ->sum('amount');

            $mExp = $mExpB2B + $mExpB2C;

            $totalRev = $mB2B + $mB2C + $mBooking;
            $revenue[] = (float) $totalRev;
            $profit[] = (float) ($totalRev - $mExp);
        }

        return ['labels' => $labels, 'revenue' => $revenue, 'profit' => $profit];
    }
}
