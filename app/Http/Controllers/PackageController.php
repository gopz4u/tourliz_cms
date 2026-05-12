<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of packages with filters.
     */
    public function index(Request $request)
    {
        $query = Package::with('destination')->where('status', 'active');

        // Search by name or destination
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhereHas('destination', function($d) use ($q) {
                        $d->where('name', 'like', "%$q%");
                    });
            });
        }

        // Filter by duration
        if ($request->filled('duration')) {
            $range = explode('-', $request->duration);
            if (count($range) == 2) {
                $query->whereBetween('duration_days', [$range[0], $range[1]]);
            } elseif ($request->duration == '10+') {
                $query->where('duration_days', '>=', 10);
            }
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort == 'price_low') $query->orderBy('price', 'asc');
        elseif ($sort == 'price_high') $query->orderBy('price', 'desc');
        else $query->latest();

        $packages = $query->paginate(12)->withQueryString();
        
        $destinations = \App\Models\Destination::where('status', 'active')->orderBy('name')->get();
        $categories = Package::whereNotNull('category')->distinct()->pluck('category');

        return view('packages.index', compact('packages', 'destinations', 'categories'));
    }

    /**
     * Display the specified package detail page.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $package = Package::with('destination')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
        
        // Get related packages (same place or category, excluding current)
        $relatedPackages = Package::with('destination')
            ->where('status', 'active')
            ->where('id', '!=', $package->id)
            ->where(function($query) use ($package) {
                if ($package->destination_id) {
                    $query->where('destination_id', $package->destination_id);
                }
                if ($package->category) {
                    $query->orWhere('category', $package->category);
                }
            })
            ->limit(3)
            ->get();
        
        return view('packages.show', compact('package', 'relatedPackages'));
    }
}
