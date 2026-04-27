<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'icon',
        'type',
        'is_global',
        'status',
        'discount_type',
        'discount_value',
        'countries',
        'image',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'countries' => 'array',
        'is_global' => 'boolean',
        'status' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return null;
        }

        if (str_contains($this->image, 'http')) {
            return $this->image;
        }

        return asset('storage/offers/' . $this->image);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_offer_package');
    }
}
