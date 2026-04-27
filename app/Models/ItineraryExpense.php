<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'itinerary_id',
        'itinerary_type',
        'supplier_id',
        'category',
        'amount',
        'currency',
        'description',
        'supplier_name',
        'expense_date',
        'created_by',
        'status',
        'vehicle_type',
        'paid_amount',
        'paid_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
