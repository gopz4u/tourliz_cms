<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageOffer;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageOfferController extends Controller
{
    public function index()
    {
        $offers = PackageOffer::with('packages')->latest()->get();
        $packages = Package::where('status', true)->get();
        $countries = Destination::whereNotNull('country')->distinct()->pluck('country');
        return view('admin.offers.index', compact('offers', 'packages', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->extension() ?: $image->getClientOriginalExtension();
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . strtolower($extension);
            $path = $image->storeAs('offers', $filename, 's3');
            $imagePath = Storage::disk('s3')->url($path);
        }

        $offer = PackageOffer::create([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'type' => $request->type,
            'is_global' => $request->has('is_global'),
            'status' => $request->has('status'),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'countries' => $request->countries,
            'image' => $imagePath,
            'published_at' => $request->published_at,
            'expires_at' => $request->expires_at,
        ]);

        if (!$request->has('is_global') && $request->has('packages')) {
            $offer->packages()->sync($request->packages);
        }

        return redirect()->back()->with('success', 'Package offer created successfully.');
    }

    public function update(Request $request, $id)
    {
        $offer = PackageOffer::findOrFail($id);
        
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'type' => $request->type,
            'is_global' => $request->has('is_global'),
            'status' => $request->has('status'),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'countries' => $request->countries,
            'published_at' => $request->published_at,
            'expires_at' => $request->expires_at,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($offer->image) {
                $oldPath = parse_url($offer->image, PHP_URL_PATH);
                if ($oldPath) {
                    Storage::disk('s3')->delete(ltrim($oldPath, '/'));
                }
            }

            $image = $request->file('image');
            $extension = $image->extension() ?: $image->getClientOriginalExtension();
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . strtolower($extension);
            $path = $image->storeAs('offers', $filename, 's3');
            $data['image'] = Storage::disk('s3')->url($path);
        }

        $offer->update($data);

        if (!$request->has('is_global')) {
            $offer->packages()->sync($request->packages ?? []);
        } else {
            $offer->packages()->detach();
        }

        return redirect()->back()->with('success', 'Package offer updated successfully.');
    }

    public function destroy($id)
    {
        $offer = PackageOffer::findOrFail($id);
        $offer->delete();

        return redirect()->back()->with('success', 'Package offer deleted successfully.');
    }
}
