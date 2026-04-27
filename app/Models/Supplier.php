<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'destination_id',
        'type',
        'contact_person',
        'phone',
        'email',
        'address',
        'bank_name',
        'account_name',
        'account_number',
        'swift_ifsc',
        'notes',
        'is_active',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function expenses()
    {
        return $this->hasMany(ItineraryExpense::class);
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function transports()
    {
        return $this->hasMany(Transport::class);
    }

    public function entryTickets()
    {
        return $this->hasMany(EntryTicket::class);
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }
}
