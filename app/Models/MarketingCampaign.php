<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingCampaign extends Model
{
    public static function ensureSchema(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('marketing_campaigns')) {
            \Illuminate\Support\Facades\Schema::create('marketing_campaigns', function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('fixed_itinerary_id')->index();
                $table->string('name');
                $table->string('message_mode', 20)->default('summary');
                $table->text('intro_message')->nullable();
                $table->string('status', 20)->default('draft');
                $table->timestamps();
            });
        }

        if (!\Illuminate\Support\Facades\Schema::hasTable('marketing_campaign_recipients')) {
            \Illuminate\Support\Facades\Schema::create('marketing_campaign_recipients', function ($table) {
                $table->id();
                $table->unsignedBigInteger('marketing_campaign_id')->index();
                $table->string('source_type', 30)->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->string('name')->nullable();
                $table->string('phone', 50);
                $table->string('status', 20)->default('pending');
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
                $table->unique(['marketing_campaign_id', 'phone'], 'marketing_campaign_phone_unique');
            });
        }
    }

    protected $fillable = ['user_id', 'fixed_itinerary_id', 'name', 'message_mode', 'intro_message', 'status'];

    public function fixedItinerary()
    {
        return $this->belongsTo(FixedItinerary::class);
    }

    public function recipients()
    {
        return $this->hasMany(MarketingCampaignRecipient::class);
    }
}
