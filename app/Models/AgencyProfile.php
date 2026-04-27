<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'logo',
        'whatsapp_number',
        'website',
        'address',
        'license_number',
        'default_markup',
        'currency',
        'primary_contact_name',
        'is_active',
    ];

    /**
     * Get the user that owns the agency profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The destinations that belong to the agency (specializations).
     */
    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'agency_destinations', 'agency_profile_id', 'destination_id')
            ->withPivot('is_specialist')
            ->withTimestamps();
    }
}
