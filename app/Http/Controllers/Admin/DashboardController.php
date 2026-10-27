<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomItinerary;
use App\Models\B2CItinerary;
use App\Models\Booking;
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
            'bookings' => Booking::count(),
            'b2b_leads' => CustomItinerary::count(),
            'b2c_leads' => B2CItinerary::count(),
            'b2b_undated' => CustomItinerary::whereIn('travel_date_status', ['tbd', 'flexible'])->count(),
            'b2c_undated' => B2CItinerary::whereIn('travel_date_status', ['tbd', 'flexible'])->count(),
            'pending_reviews' => \App\Models\Review::where('status', 'pending')->count(),
        ];
        $stats['undated_leads'] = $stats['b2b_undated'] + $stats['b2c_undated'];

        // --- Advanced Sales Analytics ---
        // Eloquent's SoftDeletes scope keeps deleted customers out of every
        // dashboard amount. These figures therefore always reflect the live
        // B2B/B2C customers currently in the database.
        $bookingRevenue = Booking::query()->sum(\DB::raw('COALESCE(total_amount, price, 0)'));
        $b2bRevenue = CustomItinerary::query()->get()->sum('myr_quoted_total');
        $b2cRevenue = B2CItinerary::query()->get()->sum('myr_quoted_total');
        $totalRevenue = $bookingRevenue + $b2bRevenue + $b2cRevenue;

        $bookingReceived = Booking::where('payment_status', 'paid')->sum(\DB::raw('COALESCE(total_amount, price, 0)')); // Simplified
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
        $outstanding = max(0, $totalRevenue - $totalReceived);
        $collectionRate = $totalRevenue > 0 ? min(100, ($totalReceived / $totalRevenue) * 100) : 0;

        $salesStats = [
            'b2b_quoted' => number_format($b2bRevenue, 2),
            'b2c_quoted' => number_format($b2cRevenue, 2),
            'booking_value' => number_format($bookingRevenue, 2),
            'total_revenue' => number_format($totalRevenue, 2),
            'received' => number_format($totalReceived, 2),
            'outstanding' => number_format($outstanding, 2),
            'collection_rate' => number_format($collectionRate, 0),
            'expenses' => number_format($totalExpenses, 2),
            'profit' => number_format($netProfit, 2),
            'conversion_rate' => $this->getConversionRate(),
        ];

        // --- Monthly Revenue Data (Last 6 Months) ---
        $chartData = $this->getMonthlyRevenueData();

        $today = Carbon::today();
        $threeDaysFromNow = Carbon::today()->addDays(3);

        // --- Reminders: Arrival in 3 Days ---
        $bookingArrivals = Booking::where('travel_date', $threeDaysFromNow)
            ->with(['package'])
            ->get();

        $b2bArrivals = CustomItinerary::where('start_date', $threeDaysFromNow)
            ->with(['agency', 'destination'])
            ->get();

        $b2cArrivals = B2CItinerary::where('start_date', $threeDaysFromNow)
            ->with(['destination'])
            ->get();

        // --- Followup Notifications (Home Page) ---
        $bookingFollowups = Booking::whereNotNull('next_followup_date')
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

        // A TravelJoy-style work queue: the dashboard should answer "what do I
        // need to do next?" without making an advisor open three modules.
        $followupCount = $bookingFollowups->count() + $b2bFollowups->count() + $b2Followups->count();

        $upcomingTrips = collect()
            ->concat(
                Booking::whereBetween('travel_date', [$today, $today->copy()->addDays(30)])
                    ->with('package.destination')
                    ->orderBy('travel_date')
                    ->take(8)
                    ->get()
                    ->map(function ($booking) {
                        return [
                            'type' => 'Booking',
                            'client' => $booking->name ?: ($booking->customer_name ?: 'Guest'),
                            'title' => optional($booking->package)->name ?: 'Custom trip',
                            'destination' => optional(optional($booking->package)->destination)->name ?: 'Destination pending',
                            'date' => $booking->travel_date,
                            'status' => $booking->payment_status ?: ($booking->status ?: 'pending'),
                            'url' => route('admin.bookings.show', $booking),
                            'tone' => 'blue',
                        ];
                    })
            )
            ->concat(
                CustomItinerary::whereBetween('start_date', [$today, $today->copy()->addDays(30)])
                    ->with(['agency', 'destination'])
                    ->orderBy('start_date')
                    ->take(8)
                    ->get()
                    ->map(function ($trip) {
                        return [
                            'type' => 'B2B',
                            'client' => optional($trip->agency)->company_name ?: ($trip->client_name ?: 'Agency client'),
                            'title' => $trip->title ?: 'Custom itinerary',
                            'destination' => optional($trip->destination)->name ?: 'Destination pending',
                            'date' => $trip->start_date,
                            'status' => $trip->payment_status ?: ($trip->status ?: 'proposal'),
                            'url' => route('admin.b2b-itineraries.edit', $trip->id),
                            'tone' => 'violet',
                        ];
                    })
            )
            ->concat(
                B2CItinerary::whereBetween('start_date', [$today, $today->copy()->addDays(30)])
                    ->with('destination')
                    ->orderBy('start_date')
                    ->take(8)
                    ->get()
                    ->map(function ($trip) {
                        return [
                            'type' => 'B2C',
                            'client' => $trip->client_name ?: 'Direct client',
                            'title' => $trip->title ?: 'Custom itinerary',
                            'destination' => optional($trip->destination)->name ?: 'Destination pending',
                            'date' => $trip->start_date,
                            'status' => $trip->payment_status ?: ($trip->status ?: 'proposal'),
                            'url' => route('admin.b2c-itineraries.edit', $trip->id),
                            'tone' => 'coral',
                        ];
                    })
            )
            ->sortBy('date')
            ->take(6)
            ->values();

        $recentActivity = collect()
            ->concat(
                Booking::with('package')->latest()->take(4)->get()->map(function ($booking) {
                    return [
                        'type' => 'Booking',
                        'client' => $booking->name ?: ($booking->customer_name ?: 'Guest'),
                        'title' => optional($booking->package)->name ?: 'Custom trip',
                        'status' => $booking->status ?: 'new',
                        'amount' => (float) ($booking->total_amount ?: $booking->price ?: 0),
                        'date' => $booking->created_at,
                        'url' => route('admin.bookings.show', $booking),
                        'tone' => 'blue',
                    ];
                })
            )
            ->concat(
                CustomItinerary::with('agency')->latest()->take(4)->get()->map(function ($trip) {
                    return [
                        'type' => 'B2B quote',
                        'client' => optional($trip->agency)->company_name ?: ($trip->client_name ?: 'Agency client'),
                        'title' => $trip->title ?: 'Custom itinerary',
                        'status' => $trip->status ?: 'proposal',
                        'amount' => (float) $trip->myr_quoted_total,
                        'date' => $trip->created_at,
                        'url' => route('admin.b2b-itineraries.edit', $trip->id),
                        'tone' => 'violet',
                    ];
                })
            )
            ->concat(
                B2CItinerary::latest()->take(4)->get()->map(function ($trip) {
                    return [
                        'type' => 'B2C quote',
                        'client' => $trip->client_name ?: 'Direct client',
                        'title' => $trip->title ?: 'Custom itinerary',
                        'status' => $trip->status ?: 'proposal',
                        'amount' => (float) $trip->myr_quoted_total,
                        'date' => $trip->created_at,
                        'url' => route('admin.b2c-itineraries.edit', $trip->id),
                        'tone' => 'coral',
                    ];
                })
            )
            ->sortByDesc('date')
            ->take(6)
            ->values();

        return view('admin.dashboard', compact(
            'stats',
            'salesStats',
            'chartData',
            'bookingArrivals',
            'b2bArrivals',
            'b2cArrivals',
            'bookingFollowups',
            'b2bFollowups',
            'b2Followups',
            'followupCount',
            'upcomingTrips',
            'recentActivity'
        ));
    }

    private function getConversionRate()
    {
        $total = Booking::count() + CustomItinerary::count() + B2CItinerary::count();
        if ($total == 0)
            return 0;

        $converted = Booking::where('followup_status', 'converted')->count() +
            CustomItinerary::where('followup_status', 'converted')->count() +
            B2CItinerary::where('followup_status', 'converted')->count();

        return number_format(($converted / $total) * 100, 1);
    }

    private function getMonthlyRevenueData()
    {
        $labels = [];
        $b2bRevenue = [];
        $b2cRevenue = [];
        $bookingRevenue = [];
        $totalRevenue = [];
        $profit = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $mB2B = CustomItinerary::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->get()
                ->sum('myr_quoted_total');

            $mB2C = B2CItinerary::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->get()
                ->sum('myr_quoted_total');

            $mBooking = Booking::whereMonth('created_at', $month->month)
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
            $b2bRevenue[] = (float) $mB2B;
            $b2cRevenue[] = (float) $mB2C;
            $bookingRevenue[] = (float) $mBooking;
            $totalRevenue[] = (float) $totalRev;
            $profit[] = (float) ($totalRev - $mExp);
        }

        return [
            'labels' => $labels,
            'b2b_revenue' => $b2bRevenue,
            'b2c_revenue' => $b2cRevenue,
            'booking_revenue' => $bookingRevenue,
            'total_revenue' => $totalRevenue,
            'profit' => $profit,
        ];
    }
}
