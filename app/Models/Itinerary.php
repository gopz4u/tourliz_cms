<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itinerary extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'itineraries';

    protected $fillable = [
        'title',
        'slug',
        'itinerary_type', // 'b2c' or 'b2b'
        'destination_id',
        'duration_days',
        'duration_nights',
        'description',
        'inclusions',
        'exclusions',
        'terms_conditions',
        'status', // draft, published, archived
        'is_published',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'inclusions' => 'array',
        'exclusions' => 'array',
        'is_published' => 'boolean',
        'duration_days' => 'integer',
        'duration_nights' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function days()
    {
        return $this->hasMany(ItineraryDay::class, 'itinerary_id')->orderBy('day_number');
    }

    public function b2bDetail()
    {
        return $this->hasOne(ItineraryB2bDetail::class, 'itinerary_id');
    }

    public function b2cDetail()
    {
        return $this->hasOne(ItineraryB2cDetail::class, 'itinerary_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    // Scopes
    public function scopeB2C($query)
    {
        return $query->where('itinerary_type', 'b2c');
    }

    public function scopeB2B($query)
    {
        return $query->where('itinerary_type', 'b2b');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('status', 'published');
    }
}
