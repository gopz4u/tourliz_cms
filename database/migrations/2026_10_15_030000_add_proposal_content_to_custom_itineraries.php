<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProposalContentToCustomItineraries extends Migration
{
    public function up()
    {
        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->text('short_description')->nullable()->after('title');
            $table->json('highlights')->nullable()->after('itinerary');
            $table->json('inclusions')->nullable()->after('highlights');
            $table->json('exclusions')->nullable()->after('inclusions');
            $table->text('terms_conditions')->nullable()->after('exclusions');
        });
    }

    public function down()
    {
        Schema::table('custom_itineraries', function (Blueprint $table) {
            $table->dropColumn(['short_description', 'highlights', 'inclusions', 'exclusions', 'terms_conditions']);
        });
    }
}
