<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'destination_id',
        'supplier_id',
        'supplier_ids',
        'category',
        'categories',
        'package_category',
        'includes_flight',
        'star_rating',
        'vehicle_type',
        'accommodation_type',
        'ticket_count',
        'ticket_name',
        'addon_amenities',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'net_price',
        'markup_percentage',
        'markup_amount',
        'discount_price',
        'price_2_6',
        'price_6_10',
        'currency',
        'duration',
        'announcement_date',
        'total_pax',
        'image',
        'gallery',
        'included_services',
        'excluded_services',
        'itinerary',
        'featured',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'supplier_ids' => 'array',
        'categories' => 'array',
        'gallery' => 'array',
        'addon_amenities' => 'array',
        'included_services' => 'array',
        'excluded_services' => 'array',
        'itinerary' => 'array',
        'price' => 'decimal:2',
        'net_price' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'markup_amount' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'price_2_6' => 'decimal:2',
        'price_6_10' => 'decimal:2',
        'featured' => 'boolean',
        'status' => 'boolean',
        'includes_flight' => 'boolean',
        'star_rating' => 'integer',
        'ticket_count' => 'integer',
        'total_pax' => 'integer',
        'announcement_date' => 'date',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the destination that owns the group package.
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    /**
     * Get is_active attribute (alias for status)
     */
    public function getIsActiveAttribute()
    {
        return $this->status;
    }

    /**
     * Get is_featured attribute (alias for featured)
     */
    public function getIsFeaturedAttribute()
    {
        return $this->featured;
    }

    /**
     * Get duration_days from duration string
     */
    public function getDurationDaysAttribute()
    {
        if (!$this->duration) {
            return null;
        }
        if (preg_match('/(\d+)\s*days?/i', $this->duration, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    /**
     * Get duration_nights from duration string
     */
    public function getDurationNightsAttribute()
    {
        if (!$this->duration) {
            return null;
        }
        if (preg_match('/(\d+)\s*nights?/i', $this->duration, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
    /**
     * Check if package has itinerary
     */
    public function hasItinerary()
    {
        return !empty($this->itinerary) && is_array($this->itinerary);
    }
}
