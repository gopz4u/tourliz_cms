<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_spend',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid()
    {
        if (!$this->is_active)
            return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit)
            return false;
        if ($this->start_date && now()->lt($this->start_date))
            return false;
        if ($this->end_date && now()->gt($this->end_date))
            return false;

        return true;
    }
}
