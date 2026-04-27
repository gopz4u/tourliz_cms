<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Destination;
use App\Models\Country;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $featuredPackages = Package::with('destination')
            ->where('status', true)
            ->where('featured', true)
            ->latest()
            ->limit(6)
            ->get();

        $allPackages = Package::with('destination')
            ->where('status', true)
            ->latest()
            ->limit(8)
            ->get();

        $topDestinations = Country::orderBy('name')->limit(6)->get();

        return view('welcome', compact('featuredPackages', 'allPackages', 'topDestinations'));
    }
}
