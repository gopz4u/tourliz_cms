<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteManagementController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->get();
        $settings = SiteSetting::all()->pluck('value', 'key');
        
        return view('admin.website.index', compact('banners', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $settings = $request->except('_token');
        
        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'text']
            );
        }

        return redirect()->back()->with('success', 'Site settings updated successfully.');
    }

    public function storeBanner(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $banner = new Banner();
        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->link = $request->link;
        $banner->order = $request->order ?? 0;
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $banner->image = $path;
        }

        $banner->save();

        return redirect()->back()->with('success', 'Banner added successfully.');
    }

    public function updateBanner(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        
        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->link = $request->link;
        $banner->order = $request->order ?? 0;
        $banner->status = $request->has('status');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $path = $request->file('image')->store('banners', 'public');
            $banner->image = $path;
        }

        $banner->save();

        return redirect()->back()->with('success', 'Banner updated successfully.');
    }

    public function destroyBanner($id)
    {
        $banner = Banner::findOrFail($id);
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();

        return redirect()->back()->with('success', 'Banner deleted successfully.');
    }
}
