<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'source',
        'campaign_name',
        'form_id',
        'leadgen_id',
        'status',
        'notes',
        'raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
    ];
}
