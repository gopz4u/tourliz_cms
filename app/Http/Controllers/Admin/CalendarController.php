<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomItinerary;
use App\Models\B2CItinerary;
use App\Models\GroupItinerary;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display the calendar index view.
     */
    public function index()
    {
        return view('admin.calendar');
    }

    /**
     * Get JSON events for FullCalendar view.
     */
    public function events(Request $request)
    {
        $startStr = $request->get('start');
        $endStr = $request->get('end');

        if (!$startStr || !$endStr) {
            return response()->json([]);
        }

        $start = Carbon::parse($startStr);
        $end = Carbon::parse($endStr);

        $events = [];

        // For departures, search start_date up to 30 days before $start
        $bufferStart = $start->copy()->subDays(30);

        // 1. B2B Custom Itineraries
        $b2bItineraries = CustomItinerary::with(['destination', 'agency'])
            ->where(function($query) use ($start, $end, $bufferStart) {
                $query->whereBetween('start_date', [$bufferStart, $end])
                      ->orWhereBetween('next_followup_date', [$start, $end]);
            })
            ->get();

        foreach ($b2bItineraries as $item) {
            $startDate = $item->start_date;
            $duration = intval($item->duration_days ?: 1);

            // B2B Arrival
            if ($startDate && $startDate->between($start, $end)) {
                $events[] = [
                    'id' => 'b2b-arr-' . $item->id,
                    'title' => '🛫 B2B: ' . ($item->client_name ?: 'Guest') . ' (Arr)',
                    'start' => $startDate->toDateString(),
                    'allDay' => true,
                    'color' => '#3b82f6', // blue
                    'extendedProps' => [
                        'type' => 'B2B',
                        'category' => 'arrival',
                        'client' => $item->client_name ?: 'Guest',
                        'agency' => $item->agency->company_name ?? 'N/A',
                        'destination' => $item->destination->name ?? 'N/A',
                        'quote_id' => $item->quote_id,
                        'price' => $item->currency . ' ' . number_format($item->total_price, 2),
                        'status' => $item->status ?? 'DRAFT',
                        'edit_url' => route('admin.b2b-itineraries.edit', $item->id)
                    ]
                ];
            }

            // B2B Departure
            if ($startDate) {
                $endDate = $startDate->copy()->addDays($duration);
                if ($endDate->between($start, $end)) {
                    $events[] = [
                        'id' => 'b2b-dep-' . $item->id,
                        'title' => '🛬 B2B: ' . ($item->client_name ?: 'Guest') . ' (Dep)',
                        'start' => $endDate->toDateString(),
                        'allDay' => true,
                        'color' => '#60a5fa', // light blue
                        'extendedProps' => [
                            'type' => 'B2B',
                            'category' => 'departure',
                            'client' => $item->client_name ?: 'Guest',
                            'agency' => $item->agency->company_name ?? 'N/A',
                            'destination' => $item->destination->name ?? 'N/A',
                            'quote_id' => $item->quote_id,
                            'price' => $item->currency . ' ' . number_format($item->total_price, 2),
                            'status' => $item->status ?? 'DRAFT',
                            'edit_url' => route('admin.b2b-itineraries.edit', $item->id)
                        ]
                    ];
                }
            }

            // B2B Followup
            if ($item->next_followup_date && $item->next_followup_date->between($start, $end)) {
                $events[] = [
                    'id' => 'b2b-fol-' . $item->id,
                    'title' => '🔔 B2B Follow-up: ' . ($item->client_name ?: 'Guest'),
                    'start' => $item->next_followup_date->toDateString(),
                    'allDay' => true,
                    'color' => '#ef4444', // red
                    'extendedProps' => [
                        'type' => 'B2B',
                        'category' => 'followup',
                        'client' => $item->client_name ?: 'Guest',
                        'agency' => $item->agency->company_name ?? 'N/A',
                        'destination' => $item->destination->name ?? 'N/A',
                        'quote_id' => $item->quote_id,
                        'price' => $item->currency . ' ' . number_format($item->total_price, 2),
                        'status' => $item->status ?? 'DRAFT',
                        'edit_url' => route('admin.b2b-itineraries.edit', $item->id)
                    ]
                ];
            }
        }

        // 2. B2C Proposals
        $b2cItineraries = B2CItinerary::with(['destination'])
            ->where(function($query) use ($start, $end, $bufferStart) {
                $query->whereBetween('start_date', [$bufferStart, $end])
                      ->orWhereBetween('next_followup_date', [$start, $end]);
            })
            ->get();

        foreach ($b2cItineraries as $item) {
            $startDate = $item->start_date;
            $duration = intval($item->duration_days ?: 1);

            // B2C Arrival
            if ($startDate && $startDate->between($start, $end)) {
                $events[] = [
                    'id' => 'b2c-arr-' . $item->id,
                    'title' => '🛫 B2C: ' . ($item->client_name ?: 'Guest') . ' (Arr)',
                    'start' => $startDate->toDateString(),
                    'allDay' => true,
                    'color' => '#10b981', // green
                    'extendedProps' => [
                        'type' => 'B2C',
                        'category' => 'arrival',
                        'client' => $item->client_name ?: 'Guest',
                        'destination' => $item->destination->name ?? 'N/A',
                        'quote_id' => $item->quote_id,
                        'price' => $item->currency . ' ' . number_format($item->total_price, 2),
                        'status' => $item->status ?? 'DRAFT',
                        'edit_url' => route('admin.b2c-itineraries.edit', $item->id)
                    ]
                ];
            }

            // B2C Departure
            if ($startDate) {
                $endDate = $startDate->copy()->addDays($duration);
                if ($endDate->between($start, $end)) {
                    $events[] = [
                        'id' => 'b2c-dep-' . $item->id,
                        'title' => '🛬 B2C: ' . ($item->client_name ?: 'Guest') . ' (Dep)',
                        'start' => $endDate->toDateString(),
                        'allDay' => true,
                        'color' => '#34d399', // light green
                        'extendedProps' => [
                            'type' => 'B2C',
                            'category' => 'departure',
                            'client' => $item->client_name ?: 'Guest',
                            'destination' => $item->destination->name ?? 'N/A',
                            'quote_id' => $item->quote_id,
                            'price' => $item->currency . ' ' . number_format($item->total_price, 2),
                            'status' => $item->status ?? 'DRAFT',
                            'edit_url' => route('admin.b2c-itineraries.edit', $item->id)
                        ]
                    ];
                }
            }

            // B2C Followup
            if ($item->next_followup_date && $item->next_followup_date->between($start, $end)) {
                $events[] = [
                    'id' => 'b2c-fol-' . $item->id,
                    'title' => '🔔 B2C Follow-up: ' . ($item->client_name ?: 'Guest'),
                    'start' => $item->next_followup_date->toDateString(),
                    'allDay' => true,
                    'color' => '#ef4444', // red
                    'extendedProps' => [
                        'type' => 'B2C',
                        'category' => 'followup',
                        'client' => $item->client_name ?: 'Guest',
                        'destination' => $item->destination->name ?? 'N/A',
                        'quote_id' => $item->quote_id,
                        'price' => $item->currency . ' ' . number_format($item->total_price, 2),
                        'status' => $item->status ?? 'DRAFT',
                        'edit_url' => route('admin.b2c-itineraries.edit', $item->id)
                    ]
                ];
            }
        }

        // 3. Group Proposals
        $groupItineraries = GroupItinerary::with(['destination'])
            ->where(function($query) use ($start, $end, $bufferStart) {
                $query->whereBetween('start_date', [$bufferStart, $end])
                      ->orWhereBetween('next_followup_date', [$start, $end]);
            })
            ->get();

        foreach ($groupItineraries as $item) {
            $startDate = $item->start_date;
            $duration = intval($item->duration_days ?: 1);

            // Group Arrival
            if ($startDate && $startDate->between($start, $end)) {
                $events[] = [
                    'id' => 'group-arr-' . $item->id,
                    'title' => '🛫 GRP: ' . ($item->client_name ?: 'Guest') . ' (Arr)',
                    'start' => $startDate->toDateString(),
                    'allDay' => true,
                    'color' => '#8b5cf6', // purple
                    'extendedProps' => [
                        'type' => 'Group',
                        'category' => 'arrival',
                        'client' => $item->client_name ?: 'Guest',
                        'destination' => $item->destination->name ?? 'N/A',
                        'quote_id' => $item->quote_id,
                        'price' => $item->currency . ' ' . number_format($item->total_price, 2),
                        'status' => $item->status ?? 'DRAFT',
                        'edit_url' => route('admin.group-itineraries.edit', $item->id)
                    ]
                ];
            }

            // Group Departure
            if ($startDate) {
                $endDate = $startDate->copy()->addDays($duration);
                if ($endDate->between($start, $end)) {
                    $events[] = [
                        'id' => 'group-dep-' . $item->id,
                        'title' => '🛬 GRP: ' . ($item->client_name ?: 'Guest') . ' (Dep)',
                        'start' => $endDate->toDateString(),
                        'allDay' => true,
                        'color' => '#c084fc', // light purple
                        'extendedProps' => [
                            'type' => 'Group',
                            'category' => 'departure',
                            'client' => $item->client_name ?: 'Guest',
                            'destination' => $item->destination->name ?? 'N/A',
                            'quote_id' => $item->quote_id,
                            'price' => $item->currency . ' ' . number_format($item->total_price, 2),
                            'status' => $item->status ?? 'DRAFT',
                            'edit_url' => route('admin.group-itineraries.edit', $item->id)
                        ]
                    ];
                }
            }

            // Group Followup
            if ($item->next_followup_date && $item->next_followup_date->between($start, $end)) {
                $events[] = [
                    'id' => 'group-fol-' . $item->id,
                    'title' => '🔔 GRP Follow-up: ' . ($item->client_name ?: 'Guest'),
                    'start' => $item->next_followup_date->toDateString(),
                    'allDay' => true,
                    'color' => '#ef4444', // red
                    'extendedProps' => [
                        'type' => 'Group',
                        'category' => 'followup',
                        'client' => $item->client_name ?: 'Guest',
                        'destination' => $item->destination->name ?? 'N/A',
                        'quote_id' => $item->quote_id,
                        'price' => $item->currency . ' ' . number_format($item->total_price, 2),
                        'status' => $item->status ?? 'DRAFT',
                        'edit_url' => route('admin.group-itineraries.edit', $item->id)
                    ]
                ];
            }
        }

        // 4. Bookings
        $bookings = Booking::with(['package'])
            ->where(function($query) use ($start, $end, $bufferStart) {
                $query->whereBetween('travel_date', [$bufferStart, $end])
                      ->orWhereBetween('next_followup_date', [$start, $end]);
            })
            ->get();

        foreach ($bookings as $item) {
            $travelDate = $item->travel_date;
            $duration = 1;
            if ($item->package) {
                $duration = intval($item->package->duration_days ?: 1);
            }

            // Booking Arrival
            if ($travelDate && $travelDate->between($start, $end)) {
                $events[] = [
                    'id' => 'booking-arr-' . $item->id,
                    'title' => '🛫 BK: ' . ($item->customer_name ?: $item->name ?: 'Customer') . ' (Arr)',
                    'start' => $travelDate->toDateString(),
                    'allDay' => true,
                    'color' => '#f97316', // orange
                    'extendedProps' => [
                        'type' => 'Booking',
                        'category' => 'arrival',
                        'client' => $item->customer_name ?: $item->name ?: 'Customer',
                        'package' => $item->package->name ?? 'Custom Package',
                        'quote_id' => $item->quote_id,
                        'price' => $item->currency . ' ' . number_format($item->total_amount ?: $item->price ?: 0, 2),
                        'status' => $item->status ?? 'PENDING',
                        'edit_url' => route('admin.bookings.show', $item->id)
                    ]
                ];
            }

            // Booking Departure
            if ($travelDate) {
                $endDate = $travelDate->copy()->addDays($duration);
                if ($endDate->between($start, $end)) {
                    $events[] = [
                        'id' => 'booking-dep-' . $item->id,
                        'title' => '🛬 BK: ' . ($item->customer_name ?: $item->name ?: 'Customer') . ' (Dep)',
                        'start' => $endDate->toDateString(),
                        'allDay' => true,
                        'color' => '#fb923c', // light orange
                        'extendedProps' => [
                            'type' => 'Booking',
                            'category' => 'departure',
                            'client' => $item->customer_name ?: $item->name ?: 'Customer',
                            'package' => $item->package->name ?? 'Custom Package',
                            'quote_id' => $item->quote_id,
                            'price' => $item->currency . ' ' . number_format($item->total_amount ?: $item->price ?: 0, 2),
                            'status' => $item->status ?? 'PENDING',
                            'edit_url' => route('admin.bookings.show', $item->id)
                        ]
                    ];
                }
            }

            // Booking Followup
            if ($item->next_followup_date && $item->next_followup_date->between($start, $end)) {
                $events[] = [
                    'id' => 'booking-fol-' . $item->id,
                    'title' => '🔔 BK Follow-up: ' . ($item->customer_name ?: $item->name ?: 'Customer'),
                    'start' => $item->next_followup_date->toDateString(),
                    'allDay' => true,
                    'color' => '#ef4444', // red
                    'extendedProps' => [
                        'type' => 'Booking',
                        'category' => 'followup',
                        'client' => $item->customer_name ?: $item->name ?: 'Customer',
                        'package' => $item->package->name ?? 'Custom Package',
                        'quote_id' => $item->quote_id,
                        'price' => $item->currency . ' ' . number_format($item->total_amount ?: $item->price ?: 0, 2),
                        'status' => $item->status ?? 'PENDING',
                        'edit_url' => route('admin.bookings.show', $item->id)
                    ]
                ];
            }
        }

        return response()->json($events);
    }
}
