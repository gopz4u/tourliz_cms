<?php

namespace App\Http\Controllers;

use App\Mail\BookingRequestMail;
use App\Models\Package;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    /**
     * Show booking form for a package (requires auth in routes).
     */
    public function showPackage($id)
    {
        $package = Package::with([
            'destination' => function ($q) {
                $q->withTrashed();
            }
        ])->findOrFail($id);

        // Get active services for add-ons (if package has place, get services for that place)
        $services = [];
        if ($package->destination_id) {
            $services = Service::where('destination_id', $package->destination_id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }

        return view('book.package', [
            'package' => $package,
            'services' => $services,
        ]);
    }

    /**
     * Handle booking submission with all parameters.
     */
    public function submitPackage(Request $request)
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

            // Coupon
            'coupon_code' => 'nullable|string',

            // Contact Method
            'contact_method' => 'nullable|in:email,whatsapp,phone,query',
            'whatsapp_number' => 'nullable|string|max:50',
            'room_id' => 'nullable|exists:hotel_rooms,id',
        ]);

        $package = Package::with([
            'destination' => function ($q) {
                $q->withTrashed();
            }
        ])->findOrFail($validated['package_id']);

        // Calculate amounts using pricing service for accuracy
        $service = new \App\Services\PackagePricingService();
        $pricingResult = $service->calculatePrice($package, $validated['adults'], $validated['room_id'] ?? null);
        
        $basePrice = $pricingResult['total_selling'];
        $addonsAmount = 0;
        $servicesAmount = 0;

        // Calculate addon services amount
        if (!empty($validated['addon_services']) && is_array($validated['addon_services'])) {
            $services = Service::whereIn('id', $validated['addon_services'])->get();
            foreach ($services as $service) {
                $servicesAmount += $service->price ?? 0;
            }
        }

        // Add-ons amount calculation (if addons have prices in the package)
        if (!empty($validated['addons']) && is_array($validated['addons'])) {
            $packageAddons = $package->addon_amenities ?? [];
            foreach ($validated['addons'] as $addonKey) {
                if (isset($packageAddons[$addonKey]) && isset($packageAddons[$addonKey]['price'])) {
                    $addonsAmount += floatval($packageAddons[$addonKey]['price']);
                }
            }
        }

        $discountAmount = 0;

        // Apply Coupon Logic
        $couponCode = null;
        if (!empty($validated['coupon_code'])) {
            $coupon = \App\Models\Coupon::where('code', $validated['coupon_code'])->first();
            $preliminaryTotal = $basePrice + $addonsAmount + $servicesAmount;

            if ($coupon && $coupon->isValid()) {
                if ($coupon->min_spend && $preliminaryTotal < $coupon->min_spend) {
                    return back()->withInput()->withErrors(['coupon_code' => 'Minimum spend of ' . $coupon->min_spend . ' required.']);
                }

                $couponCode = $coupon->code;
                if ($coupon->type == 'fixed') {
                    $discountAmount = $coupon->value;
                } elseif ($coupon->type == 'percentage') {
                    $discountAmount = ($preliminaryTotal * $coupon->value) / 100;
                }

                if ($discountAmount > $preliminaryTotal) {
                    $discountAmount = $preliminaryTotal;
                }

                $coupon->increment('used_count');
            } else {
                return back()->withInput()->withErrors(['coupon_code' => 'Invalid or expired coupon code.']);
            }
        }

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

        // Prepare booking data
        $bookingData = [
            'package_id' => $package->id,
            'hotel_room_id' => $validated['room_id'] ?? null,
            'admin_id' => $request->user()->id ?? null,
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
            'coupon_code' => $couponCode,
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
        ];

        // Persist booking
        $booking = Booking::create($bookingData);

        // Prepare email payload with all details
        $payload = [
            'booking' => $booking,
            'package' => $package,
            'user' => $request->user(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? '',
            'travel_date' => $validated['travel_date'],
            'adults' => $validated['adults'],
            'children' => $validated['children'] ?? 0,
            'notes' => $validated['notes'] ?? '',
            'customer_address' => $validated['customer_address'] ?? null,
            'customer_city' => $validated['customer_city'] ?? null,
            'customer_state' => $validated['customer_state'] ?? null,
            'customer_country' => $validated['customer_country'] ?? null,
            'customer_postal_code' => $validated['customer_postal_code'] ?? null,
            'addons' => $validated['addons'] ?? [],
            'addon_services' => !empty($validated['addon_services']) ? Service::whereIn('id', $validated['addon_services'])->get() : collect(),
            'payment_status' => $validated['payment_status'] ?? 'pending',
            'payment_details' => $paymentDetails,
            'contact_method' => $validated['contact_method'] ?? 'email',
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'base_price' => $basePrice,
            'addons_amount' => $addonsAmount,
            'services_amount' => $servicesAmount,
            'total_amount' => $totalAmount,
        ];

        // Send email to admin (fallback to app email)
        $adminEmail = config('mail.from.address');
        if (!$adminEmail) {
            $adminEmail = $validated['email']; // fallback to user email to avoid failing
        }

        try {
            Mail::to($adminEmail)->send(new BookingRequestMail($payload));
            Log::info('Booking request sent', [
                'booking_id' => $booking->id,
                'package_id' => $package->id,
                'user_id' => $request->user()->id ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking email: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Could not send booking request. Please try again later.']);
        }

        return redirect()->route('book.package.show', ['id' => $package->id])
            ->with('success', 'Booking request sent successfully. We will contact you shortly.');
    }

    /**
     * Check coupon validity via AJAX.
     */
    public function checkCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $coupon = \App\Models\Coupon::where('code', $request->coupon_code)->first();

        if (!$coupon || !$coupon->isValid()) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon.']);
        }

        if ($coupon->min_spend && $request->amount < $coupon->min_spend) {
            return response()->json(['valid' => false, 'message' => 'Minimum spend of ' . $coupon->min_spend . ' required.']);
        }

        $discount = 0;
        if ($coupon->type == 'fixed') {
            $discount = $coupon->value;
        } else {
            $discount = ($request->amount * $coupon->value) / 100;
        }

        if ($discount > $request->amount) {
            $discount = $request->amount;
        }

        return response()->json([
            'valid' => true,
            'discount' => $discount,
            'message' => 'Coupon applied successfully!',
            'type' => $coupon->type,
            'value' => $coupon->value
        ]);
    }

    /**
     * Handle Get a Quote form submission via AJAX.
     */
    public function getQuote(Request $request, $slug)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'travel_date' => 'required|date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'message' => 'nullable|string|max:1000',
        ]);

        $package = Package::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();

        // Calculate subtotal
        $subtotal = ($package->price ?? 0) * $request->adults;
        
        // Save to database
        $booking = Booking::create([
            'package_id' => $package->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'travel_date' => $request->travel_date,
            'adults' => $request->adults,
            'children' => $request->children ?? 0,
            'notes' => $request->message,
            'status' => 'pending',
            'base_price' => $package->price ?? 0,
            'total_amount' => $subtotal,
            'currency' => $package->currency ?? 'USD',
            'contact_method' => 'query',
        ]);

        // Send email to admin
        $payload = [
            'booking' => $booking,
            'package' => $package,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'travel_date' => $request->travel_date,
            'adults' => $request->adults,
            'children' => $request->children ?? 0,
            'notes' => $request->message,
            'total_amount' => $subtotal,
            'contact_method' => 'query',
        ];

        $adminEmail = config('mail.from.address');
        
        try {
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new BookingRequestMail($payload));
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Your quote request has been submitted successfully. We will contact you soon!',
                'booking_id' => $booking->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send quote request email: ' . $e->getMessage());
            // Still return success as the booking is saved
            return response()->json([
                'success' => true,
                'message' => 'Your quote request has been received, but we encountered an issue sending the notification email. Our team will still review your request!',
                'booking_id' => $booking->id,
            ]);
        }
    }

    /**
     * Calculate price dynamically for frontend.
     */
    public function calculatePricing(Request $request, $slug)
    {
        $request->validate([
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'room_config' => 'nullable|string|in:single,double,triple,quad',
            'room_id' => 'nullable|exists:hotel_rooms,id',
        ]);

        $package = Package::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $paxCount = $request->adults;
        
        // Use the new Pricing Service
        $service = new \App\Services\PackagePricingService();
        $result = $service->calculatePrice($package, $paxCount, $request->room_id);

        // Optional: Manual Room Type Override logic
        // If the user selects 'triple' but there are 4 people, 
        // the service usually handles it, but we can refine here if needed.

        $currency = $package->currency ?? 'MYR';
        
        return response()->json([
            'success' => true,
            'per_pax' => \App\Helpers\CurrencyHelper::format($result['per_pax'], $currency),
            'total' => \App\Helpers\CurrencyHelper::format($result['total_selling'], $currency),
            'raw_per_pax' => $result['per_pax'],
            'raw_total' => $result['total_selling']
        ]);
    }
}

