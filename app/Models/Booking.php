<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($booking) {
            // Synchronize name, email, phone with customer_* fields
            if (empty($booking->customer_name) && !empty($booking->name)) {
                $booking->customer_name = $booking->name;
            }
            if (empty($booking->customer_email) && !empty($booking->email)) {
                $booking->customer_email = $booking->email;
            }
            if (empty($booking->customer_phone) && !empty($booking->phone)) {
                $booking->customer_phone = $booking->phone;
            }

            if (empty($booking->name) && !empty($booking->customer_name)) {
                $booking->name = $booking->customer_name;
            }
            if (empty($booking->email) && !empty($booking->customer_email)) {
                $booking->email = $booking->customer_email;
            }
            if (empty($booking->phone) && !empty($booking->customer_phone)) {
                $booking->phone = $booking->customer_phone;
            }

            // Fallback for number_of_people
            if ($booking->number_of_people === null || $booking->number_of_people === '') {
                $booking->number_of_people = ($booking->adults ?? 1) + ($booking->children ?? 0);
            }

            // Fallback for package_price
            if ($booking->package_price === null || $booking->package_price === '') {
                $booking->package_price = $booking->price ?? $booking->base_price ?? 0;
            }
        });
    }

    protected $fillable = [
        'package_id',
        'hotel_room_id',
        'admin_id',
        'name',
        'email',
        'phone',
        'travel_date',
        'adults',
        'children',
        'notes',
        'status',
        'price',
        'base_price',
        'currency',
        'coupon_code',
        // Add-ons and Services
        'addons',
        'addon_services',
        // Customer Details
        'customer_address',
        'customer_city',
        'customer_state',
        'customer_country',
        'customer_postal_code',
        // Payment Information
        'payment_status',
        'payment_details',
        'addons_amount',
        'services_amount',
        'discount_amount',
        'total_amount',
        // Contact Method
        'contact_method',
        'whatsapp_number',
        // Follow-up
        'followup_status',
        'followed_up_at',
        'next_followup_date',
        'kids_2_6',
        'kids_6_10',
        'customer_name',
        'customer_email',
        'customer_phone',
        'address',
        'total_amount',
    ];

    /**
     * Get unique Quote/Booking ID: BK-YYYYMMDD-ID
     */
    public function getQuoteIdAttribute()
    {
        $date = $this->created_at ? $this->created_at->format('Ymd') : date('Ymd');
        return "BK-{$date}-" . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    protected $casts = [
        'addons' => 'array',
        'addon_services' => 'array',
        'payment_details' => 'array',
        'travel_date' => 'date',
        'price' => 'decimal:2',
        'base_price' => 'decimal:2',
        'addons_amount' => 'decimal:2',
        'services_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'followed_up_at' => 'datetime',
        'next_followup_date' => 'date',
        'kids_2_6' => 'integer',
        'kids_6_10' => 'integer',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}

