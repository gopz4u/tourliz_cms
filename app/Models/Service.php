<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'destination_id',
        'package_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'price_2_6',
        'price_6_10',
        'currency',
        'announcement_date',
        'total_pax',
        'image',
        'gallery',
        'category',
        'star_rating',
        'vehicle_type',
        'accommodation_type',
        'ticket_count',
        'ticket_name',
        'addon_amenities',
        'icon',
        'is_featured',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'gallery' => 'array',
        'addon_amenities' => 'array',
        'price' => 'decimal:2',
        'price_2_6' => 'decimal:2',
        'price_6_10' => 'decimal:2',
        'announcement_date' => 'date',
        'total_pax' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'star_rating' => 'integer',
        'ticket_count' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the destination that owns the service.
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    /**
     * Get the package that owns the service.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
