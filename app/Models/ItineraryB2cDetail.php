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
        'is_template',
        'quotation_reference',
        'lead_id',
        'customer_name',
        'customer_whatsapp',
        'customer_email',
        'travel_date_start',
        'travel_date_end',
        'adults',
        'children',
        'rooms',
        'lead_source',
        'assigned_staff_id',
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
        'is_template' => 'boolean',
        'travel_date_start' => 'date',
        'travel_date_end' => 'date',
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

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Admin::class, 'assigned_staff_id');
    }
}
