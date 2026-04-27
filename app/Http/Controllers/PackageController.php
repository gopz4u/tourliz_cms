<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
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
            ->where('status', true)
            ->firstOrFail();
        
        // Get related packages (same place or category, excluding current)
        $relatedPackages = Package::with('destination')
            ->where('status', true)
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
