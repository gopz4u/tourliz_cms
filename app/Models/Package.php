<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'destination_id',
        'destination_ids',
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
        'announcement_date',
        'total_pax',
        'duration',
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
        'destination_ids' => 'array',
        'supplier_ids' => 'array',
        'categories' => 'array',
        'gallery' => 'array',
        'addon_amenities' => 'array',
        'included_services' => 'array',
        'excluded_services' => 'array',
        'itinerary' => 'array',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'price_2_6' => 'decimal:2',
        'price_6_10' => 'decimal:2',
        'announcement_date' => 'date',
        'total_pax' => 'integer',
        'featured' => 'boolean',
        'status' => 'boolean',
        'includes_flight' => 'boolean',
        'star_rating' => 'integer',
        'ticket_count' => 'integer',
    ];

    protected $appends = [
        'average_rating',
        'reviews_count'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the destination that owns the package.
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function offers()
    {
        return $this->belongsToMany(PackageOffer::class, 'package_offer_package');
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
     * Get formatted itinerary with enriched data
     */
    public function getFormattedItineraryAttribute()
    {
        if (!$this->itinerary) {
            return null;
        }

        return \App\Helpers\ItineraryHelper::enrichItinerary($this->itinerary);
    }

    /**
     * Get itinerary cost breakdown
     */
    public function getItineraryCostBreakdownAttribute()
    {
        if (!$this->itinerary) {
            return null;
        }

        return \App\Helpers\ItineraryHelper::calculateTotalCost($this->itinerary, $this->currency);
    }

    /**
     * Check if package has itinerary
     */
    public function hasItinerary()
    {
        return !empty($this->itinerary) && is_array($this->itinerary);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?: 5, 1);
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }
}
