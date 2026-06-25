<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedItinerary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'supplier_id',
        'country_ids',
        'title',
        'itinerary_description',
        'fixed_price',
        'currency',
        'status',
    ];

    protected $casts = [
        'country_ids' => 'array',
        'fixed_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getCountriesAttribute()
    {
        $ids = $this->country_ids ?: [];
        if (empty($ids)) {
            return collect();
        }
        return Country::whereIn('id', $ids)->get();
    }
}
