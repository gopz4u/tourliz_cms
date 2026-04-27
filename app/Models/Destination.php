<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destination extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'destinations';

    protected $fillable = [
        'name',
        'country',
        'location',
        'city',
        'slug',
        'description',
        'short_description',
        'image',
        'gallery',
        'price',
        'rating',
        'featured',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'gallery' => 'array',
        'featured' => 'boolean',
        'status' => 'boolean',
        'rating' => 'integer',
        'price' => 'decimal:2',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the packages for the destination.
     */
    public function packages()
    {
        return $this->hasMany(Package::class, 'destination_id');
    }

    /**
     * Get the country model for this destination.
     */
    public function countryRel()
    {
        return $this->belongsTo(Country::class, 'country', 'name');
    }
}
