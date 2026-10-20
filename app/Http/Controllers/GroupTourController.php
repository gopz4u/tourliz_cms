<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\GroupPackage;
use Illuminate\Http\Request;

class GroupTourController extends Controller
{
    /**
     * Display published group tours that still have a bookable future departure.
     */
    public function index(Request $request)
    {
        $query = GroupPackage::query()
            ->with(['destination', 'fixedItinerary', 'departures' => function ($query) {
                $this->upcomingDepartures($query)
                    ->withSum(['customers as booked_pax' => function ($customers) {
                        $customers->where('booking_status', '!=', 'cancelled');
                    }], 'pax');
            }])
            ->withMin(['departures as next_departure_date' => function ($query) {
                $this->upcomingDepartures($query);
            }], 'departure_date')
            ->where('status', true)
            ->whereHas('departures', function ($query) {
                $this->upcomingDepartures($query);
            });

        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhereHas('destination', function ($destination) use ($search) {
                        $destination->where('name', 'like', "%{$search}%")
                            ->orWhere('country', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('destination')) {
            $query->where('destination_id', (int) $request->input('destination'));
        }

        if ($request->input('sort') === 'price_low') {
            $query->orderBy('price');
        } elseif ($request->input('sort') === 'price_high') {
            $query->orderByDesc('price');
        } else {
            $query->orderBy('next_departure_date');
        }

        $groupTours = $query->paginate(12)->withQueryString();
        $destinations = Destination::whereHas('groupPackages', function ($packages) {
            $packages->where('status', true)
                ->whereHas('departures', function ($departures) {
                    $this->upcomingDepartures($departures);
                });
        })->orderBy('name')->get();

        return view('group-tours.index', compact('groupTours', 'destinations'));
    }

    /**
     * Display one published group tour and its upcoming departures.
     */
    public function show(string $slug)
    {
        $groupTour = GroupPackage::query()
            ->with(['destination', 'fixedItinerary', 'departures' => function ($query) {
                $this->upcomingDepartures($query, true)
                    ->withSum(['customers as booked_pax' => function ($customers) {
                        $customers->where('booking_status', '!=', 'cancelled');
                    }], 'pax');
            }])
            ->where('slug', $slug)
            ->where('status', true)
            ->whereHas('departures', function ($query) {
                $this->upcomingDepartures($query);
            })
            ->firstOrFail();

        $relatedTours = GroupPackage::query()
            ->with(['destination', 'departures' => function ($query) {
                $this->upcomingDepartures($query)
                    ->withSum(['customers as booked_pax' => function ($customers) {
                        $customers->where('booking_status', '!=', 'cancelled');
                    }], 'pax');
            }])
            ->where('status', true)
            ->where('id', '!=', $groupTour->id)
            ->when($groupTour->destination_id, function ($query) use ($groupTour) {
                $query->where('destination_id', $groupTour->destination_id);
            })
            ->whereHas('departures', function ($query) {
                $this->upcomingDepartures($query);
            })
            ->limit(3)
            ->get();

        return view('group-tours.show', compact('groupTour', 'relatedTours'));
    }

    private function upcomingDepartures($query, bool $includeSoldOut = false)
    {
        $statuses = $includeSoldOut ? ['announced', 'open', 'sold_out'] : ['announced', 'open'];

        return $query
            ->whereDate('departure_date', '>=', today())
            ->whereIn('status', $statuses)
            ->orderBy('departure_date');
    }
}
