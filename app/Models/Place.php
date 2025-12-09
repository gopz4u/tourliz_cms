<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'location',
        'region',
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
}
