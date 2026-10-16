<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingCampaignRecipient extends Model
{
    protected $fillable = ['marketing_campaign_id', 'source_type', 'source_id', 'name', 'phone', 'status', 'sent_at'];

    protected $casts = ['sent_at' => 'datetime'];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }
}
