<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationCampaign extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'url',
        'recipient_scope',
        'recipient_count',
        'in_app_count',
        'push_count',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'recipient_count' => 'integer',
        'in_app_count' => 'integer',
        'push_count' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
}
