<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Mail\ReviewApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['package', 'user'])->latest()->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
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

        return back()->with('success', 'Review status updated successfully.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
