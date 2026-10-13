<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupDepartureCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['group_departure_id', 'name', 'phone', 'email', 'passport_number', 'date_of_birth', 'customer_type', 'pax', 'rate', 'amount_paid', 'payment_status', 'booking_status', 'notes'];

    protected $casts = ['date_of_birth' => 'date', 'pax' => 'integer', 'rate' => 'decimal:2', 'amount_paid' => 'decimal:2'];

    public function departure() { return $this->belongsTo(GroupDeparture::class, 'group_departure_id'); }

    public function getBalanceAttribute(): float
    {
        return max(0, ((float) $this->rate * $this->pax) - (float) $this->amount_paid);
    }
}
