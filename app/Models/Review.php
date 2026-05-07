<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'destination_id',
        'user_id',
        'booking_id',
        'name',
        'email',
        'title',
        'image',
        'rating',
        'comment',
        'status',
        'is_testimonial',
        'is_featured',
        'media'
    ];

    protected $casts = [
        'media' => 'array',
        'is_featured' => 'boolean'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
