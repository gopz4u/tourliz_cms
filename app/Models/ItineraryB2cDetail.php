<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryB2cDetail extends Model
{
    use HasFactory;

    protected $table = 'itinerary_b2c_details';

    protected $fillable = [
        'itinerary_id',
        'short_description',
        'selling_price',
        'child_price',
        'image',
        'gallery',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'child_price' => 'decimal:2',
        'gallery' => 'array',
    ];

    protected $appends = ['image_url'];

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id');
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        $cdnUrl = env('AWS_URL');
        if ($cdnUrl) {
            return rtrim($cdnUrl, '/') . '/' . ltrim($this->image, '/');
        }

        return url('storage/' . ltrim($this->image, '/'));
    }
}
