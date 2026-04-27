<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
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

