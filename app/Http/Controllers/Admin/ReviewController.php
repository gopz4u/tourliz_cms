<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Mail\ReviewApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['package', 'destination', 'user', 'booking']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured == 'yes');
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();
        
        return view('admin.reviews.index', compact('reviews'));
    }

    public function create()
    {
        $packages = \App\Models\Package::all();
        $destinations = \App\Models\Destination::all();
        return view('admin.reviews.create', compact('packages', 'destinations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('reviews', 'public');
        }

        Review::create($data);
        return redirect()->route('admin.reviews.index')->with('success', 'Review created successfully.');
    }

    public function edit($id)
    {
        $review = Review::findOrFail($id);
        $packages = \App\Models\Package::all();
        $destinations = \App\Models\Destination::all();
        return view('admin.reviews.edit', compact('review', 'packages', 'destinations'));
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('reviews', 'public');
        }

        $review->update($data);
        return redirect()->route('admin.reviews.index')->with('success', 'Review updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->update(['status' => $request->status]);
        
        // Send email to user if approved
        if ($request->status == 'approved' && $review->email) {
            try {
                Mail::to($review->email)->send(new ReviewApproved($review));
            } catch (\Exception $e) {
                \Log::error("Failed to send review approval email: " . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    public function toggleFeatured($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_featured' => !$review->is_featured]);
        
        return response()->json(['success' => true, 'featured' => $review->is_featured]);
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->ids;
        $action = $request->action;

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No reviews selected.'], 422);
        }

        switch ($action) {
            case 'approve':
                Review::whereIn('id', $ids)->update(['status' => 'approved']);
                // Send emails for approved ones if needed (optional for bulk)
                break;
            case 'reject':
                Review::whereIn('id', $ids)->update(['status' => 'rejected']);
                break;
            case 'delete':
                Review::whereIn('id', $ids)->delete();
                break;
            case 'feature':
                Review::whereIn('id', $ids)->update(['is_featured' => true]);
                break;
            case 'unfeature':
                Review::whereIn('id', $ids)->update(['is_featured' => false]);
                break;
        }

        return response()->json(['success' => true, 'message' => 'Bulk action completed successfully.']);
    }

    public function show($id)
    {
        $review = Review::with(['package', 'destination', 'user', 'booking'])->findOrFail($id);
        return view('admin.reviews.show', compact('review'));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
