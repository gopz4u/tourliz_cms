<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attraction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'offer_price',
        'price_2_6',
        'price_6_10',
        'currency',
        'announcement_date',
        'total_pax',
        'image',
        'gallery',
        'short_description',
        'description',
        'destination_id',
        'package_id',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'gallery' => 'array',
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'price_2_6' => 'decimal:2',
        'price_6_10' => 'decimal:2',
        'announcement_date' => 'date',
        'total_pax' => 'integer',
        'status' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the destination that owns the attraction.
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    /**
     * Get the package that owns the attraction.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get is_active attribute (alias for status)
     */
    public function getIsActiveAttribute()
    {
        return $this->status;
    }
}
