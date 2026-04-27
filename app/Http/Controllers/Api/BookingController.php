<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Get authenticated user's bookings
     */
    public function index(Request $request)
    {
        $bookings = Booking::where('admin_id', $request->user()->id)
            ->with(['package.place'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'data' => $bookings->items(),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    /**
     * Get a specific booking
     */
    public function show(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('admin_id', $request->user()->id)
            ->with(['package.place'])
            ->firstOrFail();

        return response()->json([
            'data' => $booking,
        ]);
    }

    /**
     * Submit a new booking (requires authentication)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'travel_date' => 'required|date',
            'adults' => 'required|integer|min:1|max:20',
            'children' => 'nullable|integer|min:0|max:20',
            'notes' => 'nullable|string|max:2000',
            
            // Customer Details
            'customer_address' => 'nullable|string|max:500',
            'customer_city' => 'nullable|string|max:100',
            'customer_state' => 'nullable|string|max:100',
            'customer_country' => 'nullable|string|max:100',
            'customer_postal_code' => 'nullable|string|max:20',
            
            // Add-ons and Services
            'addons' => 'nullable|array',
            'addons.*' => 'nullable|string',
            'addon_services' => 'nullable|array',
            'addon_services.*' => 'nullable|integer|exists:services,id',
            
            // Payment Information
            'payment_status' => 'nullable|in:pending,paid,partially_paid,refunded',
            'payment_method' => 'nullable|string|max:100',
            'payment_transaction_id' => 'nullable|string|max:255',
            'payment_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            
            // Contact Method
            'contact_method' => 'nullable|in:email,whatsapp,phone,query',
            'whatsapp_number' => 'nullable|string|max:50',
        ]);

        $package = Package::with(['destination'])->findOrFail($validated['package_id']);

        // Calculate amounts
        $basePrice = $package->price ?? 0;
        $addonsAmount = 0;
        $servicesAmount = 0;
        
        // Calculate addon services amount
        if (!empty($validated['addon_services']) && is_array($validated['addon_services'])) {
            $services = Service::whereIn('id', $validated['addon_services'])->get();
            foreach ($services as $service) {
                $servicesAmount += $service->price ?? 0;
            }
        }
        
        // Add-ons amount calculation
        if (!empty($validated['addons']) && is_array($validated['addons'])) {
            $packageAddons = $package->addon_amenities ?? [];
            foreach ($validated['addons'] as $addonKey) {
                if (isset($packageAddons[$addonKey]) && isset($packageAddons[$addonKey]['price'])) {
                    $addonsAmount += floatval($packageAddons[$addonKey]['price']);
                }
            }
        }
        
        $discountAmount = 0;
        $totalAmount = $basePrice + $addonsAmount + $servicesAmount - $discountAmount;

        // Prepare payment details if payment was made
        $paymentDetails = null;
        if (!empty($validated['payment_status']) && in_array($validated['payment_status'], ['paid', 'partially_paid'])) {
            $paymentDetails = [
                'method' => $validated['payment_method'] ?? null,
                'transaction_id' => $validated['payment_transaction_id'] ?? null,
                'amount' => $validated['payment_amount'] ?? $totalAmount,
                'date' => $validated['payment_date'] ?? now()->toDateString(),
            ];
        }

        // Create booking
        $booking = Booking::create([
            'package_id' => $package->id,
            'admin_id' => $request->user()->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'travel_date' => $validated['travel_date'],
            'adults' => $validated['adults'],
            'children' => $validated['children'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'price' => $basePrice,
            'base_price' => $basePrice,
            'currency' => $package->currency ?? 'USD',
            'addons' => $validated['addons'] ?? null,
            'addon_services' => $validated['addon_services'] ?? null,
            'customer_address' => $validated['customer_address'] ?? null,
            'customer_city' => $validated['customer_city'] ?? null,
            'customer_state' => $validated['customer_state'] ?? null,
            'customer_country' => $validated['customer_country'] ?? null,
            'customer_postal_code' => $validated['customer_postal_code'] ?? null,
            'payment_status' => $validated['payment_status'] ?? 'pending',
            'payment_details' => $paymentDetails,
            'addons_amount' => $addonsAmount,
            'services_amount' => $servicesAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'contact_method' => $validated['contact_method'] ?? 'email',
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
        ]);

        Log::info('API Booking created', [
            'booking_id' => $booking->id,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => $booking->load('package.place'),
        ], 201);
    }
}

