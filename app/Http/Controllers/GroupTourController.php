<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\GroupPackage;
use Illuminate\Http\Request;

class GroupTourController extends Controller
{
    /** Display published Website Group Tours with their own future date. */
    public function index(Request $request)
    {
        $query = GroupPackage::query()
            ->with('destination')
            ->visibleOnWebsite();

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
            $query->orderBy('departure_date');
        }

        $groupTours = $query->paginate(12)->withQueryString();
        $destinations = Destination::whereHas('groupPackages', function ($packages) {
            $packages->visibleOnWebsite();
        })->orderBy('name')->get();

        return view('group-tours.index', compact('groupTours', 'destinations'));
    }

    /** Display one published Website Group Tour. */
    public function show(string $slug)
    {
        $groupTour = GroupPackage::query()
            ->with(['destination', 'fixedItinerary'])
            ->where('slug', $slug)
            ->visibleOnWebsite()
            ->firstOrFail();

        $relatedTours = GroupPackage::query()
            ->with('destination')
            ->visibleOnWebsite()
            ->where('id', '!=', $groupTour->id)
            ->when($groupTour->destination_id, function ($query) use ($groupTour) {
                $query->where('destination_id', $groupTour->destination_id);
            })
            ->orderBy('departure_date')
            ->limit(3)
            ->get();

        return view('group-tours.show', compact('groupTour', 'relatedTours'));
    }
}
