<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingFollowupMail;
use App\Mail\BookingConfirmationMail;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'package_id' => 'required|exists:packages,id',
            'travel_date' => 'required|date',
            'next_followup_date' => 'nullable|date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'kids_2_6' => 'nullable|integer|min:0',
            'kids_6_10' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,cancelled',
            'followup_status' => 'required|in:leads,followed_up,waiting,interested,not_interested,converted,dead',
        ]);

        $booking = new Booking($validated);

        // Fetch package to get price if needed
        $package = \App\Models\Package::find($request->package_id);
        if ($package) {
            $price = $package->discount_price ?: $package->price;
            $booking->package_price = $package->price;
            $booking->discount_price = $package->discount_price;
            $booking->currency = $package->currency ?: 'INR';

            // Basic calculation for total amount if not manually set
            $total = ($request->adults * $price);
            if ($request->kids_2_6)
                $total += ($request->kids_2_6 * ($package->price_2_6 ?: $price * 0.5));
            if ($request->kids_6_10)
                $total += ($request->kids_6_10 * ($package->price_6_10 ?: $price * 0.75));

            $booking->total_amount = $total;
        }

        $booking->admin_id = auth()->id();
        $booking->save();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully.');
    }

    public function create()
    {
        $packages = \App\Models\Package::orderBy('name')->get();
        return view('admin.bookings.create', compact('packages'));
    }

    public function show(Booking $booking)
    {
        // Eager load relationships to avoid N+1 queries
        $booking->load([
            'package' => function ($q) {
                $q->withTrashed()->with([
                    'destination' => function ($p) {
                        $p->withTrashed();
                    }
                ]);
            },
            'admin'
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    public function index(Request $request)
    {
        // 1. Start with a clean query
        $query = Booking::query();

        // 2. Eager load relationships safely (including soft deletes)
        $query->with([
            'package' => function ($q) {
                $q->withTrashed()->with([
                    'destination' => function ($p) {
                        $p->withTrashed();
                    }
                ]);
            },
            'admin'
        ]);

        // 3. Apply Filters
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('id', $search) // Exact ID match often better than like for ID
                    ->orWhere('id', 'like', "%{$search}%");

                // Handle Quote ID search (BK-YYYYMMDD-ID)
                if (preg_match('/BK-\d+-\d+/', $search)) {
                    $id = (int) substr($search, strrpos($search, '-') + 1);
                    $sub->orWhere('id', $id);
                }
            });
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->filled('payment_status'), function ($q) use ($request) {
            $q->where('payment_status', $request->payment_status);
        });

        $query->when($request->filled('source'), function ($q) use ($request) {
            $q->where('source', $request->source);
        });

        // 4. Get Results (Pagination)
        $bookings = $query->orderByDesc('created_at')->paginate(100);

        // 5. API Response (JSON)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($bookings);
        }

        // 6. View Data
        $newCount = Booking::where('status', 'pending')->count();
        $totalCount = Booking::count();
        $recentCount = Booking::where('created_at', '>=', now()->subDays(2))->count();

        return view('admin.bookings.index', compact('bookings', 'newCount', 'totalCount', 'recentCount'));
    }



    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'cancelled'])],
            'followup_status' => ['nullable', Rule::in(['leads', 'followed_up', 'waiting', 'interested', 'not_interested', 'converted', 'dead'])],
            'next_followup_date' => ['nullable', 'date'],
        ]);

        $emailTrigger = null; // 'confirmation' or 'followup'
        $recipientEmail = $booking->email ?: $booking->customer_email;

        if ($request->filled('status')) {
            // Check if status is changing TO confirmed
            if ($booking->status !== 'confirmed' && $validated['status'] === 'confirmed') {
                $emailTrigger = 'confirmation';
            }
            $booking->status = $validated['status'];
        }

        if ($request->filled('followup_status')) {
            $booking->followup_status = $validated['followup_status'];
            $booking->followed_up_at = now();
        }

        if ($request->filled('next_followup_date')) {
            $booking->next_followup_date = $validated['next_followup_date'];
            // Trigger follow-up email if not already sending confirmation
            if (!$emailTrigger && $validated['next_followup_date']) {
                $emailTrigger = 'followup';
            }
        }

        $booking->save();

        // Send Automated Emails
        if ($recipientEmail && $emailTrigger) {
            try {
                if ($emailTrigger === 'confirmation') {
                    Mail::to($recipientEmail)->send(new BookingConfirmationMail($booking));
                } elseif ($emailTrigger === 'followup') {
                    Mail::to($recipientEmail)->send(new BookingFollowupMail($booking));
                }
            } catch (\Exception $e) {
                // Log error but don't stop the response
                \Illuminate\Support\Facades\Log::error('Failed to send booking email: ' . $e->getMessage());
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully.',
                'booking' => $booking->fresh(['package.place', 'admin'])
            ]);
        }

        return redirect()->back()->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        \Log::info("BookingController@destroy called for booking ID: " . ($booking ? $booking->id : 'null'));

        if (!auth()->user()->isSuperAdmin()) {
            \Log::warning("Unauthorized booking deletion attempt by user ID: " . auth()->id());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => 'Unauthorized access.'], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        try {
            $booking->delete();
            \Log::info("Booking ID: " . $booking->id . " successfully deleted.");

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking deleted successfully.'
                ]);
            }

            return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully.');
        } catch (\Throwable $e) {
            \Log::error('Failed to delete booking ID ' . $booking->id . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete booking: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete booking: ' . $e->getMessage());
        }
    }
}

