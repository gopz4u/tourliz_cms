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

    public function getCategoryLabelAttribute(): string
    {
        $t = strtolower(trim($this->type ?? 'other'));
        if (strpos($t, 'hotel') !== false) return 'Hotel';
        if (strpos($t, 'transport') !== false || strpos($t, 'vehicle') !== false || strpos($t, 'transfer') !== false) return 'Transport';
        if (strpos($t, 'ticket') !== false || strpos($t, 'entry') !== false || strpos($t, 'attraction') !== false) return 'Entry Tickets';
        if (strpos($t, 'activity') !== false || strpos($t, 'tour') !== false) return 'Activities';
        if (strpos($t, 'meal') !== false || strpos($t, 'food') !== false || strpos($t, 'restaurant') !== false) return 'Meals';
        return ucfirst($this->type ?: 'Other');
    }

    public function getCategoryIconAttribute(): string
    {
        $cat = $this->category_label;
        $icons = [
            'Hotel' => 'bi-building',
            'Transport' => 'bi-car-front',
            'Entry Tickets' => 'bi-ticket-perforated',
            'Activities' => 'bi-lightning',
            'Meals' => 'bi-cup-hot',
        ];
        return $icons[$cat] ?? 'bi-box';
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        $cat = $this->category_label;
        $classes = [
            'Hotel' => 'bg-primary-subtle text-primary border-primary-subtle',
            'Transport' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
            'Entry Tickets' => 'bg-danger-subtle text-danger border-danger-subtle',
            'Activities' => 'bg-warning-subtle text-dark border-warning-subtle',
            'Meals' => 'bg-success-subtle text-success border-success-subtle',
        ];
        return $classes[$cat] ?? 'bg-light text-dark border';
    }

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
