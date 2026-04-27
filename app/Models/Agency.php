<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'primary_contact_name',
        'whatsapp_number',
        'logo',
        'website',
        'address',
        'default_markup',
        'currency',
        'is_active',
    ];

    /**
     * Get the specialized destinations for this agency.
     */
    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'agency_destinations', 'agency_id', 'destination_id')
            ->withPivot('is_specialist')
            ->withTimestamps();
    }

    /**
     * Get the itineraries created for this agency.
     */
    public function itineraries()
    {
        return $this->hasMany(CustomItinerary::class);
    }
}
