<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsAndDaysToFixedItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fixed_itineraries', function (Blueprint $table) {
            if (!Schema::hasColumn('fixed_itineraries', 'destination_id')) {
                $table->foreignId('destination_id')->nullable()->after('supplier_id')->constrained('destinations')->nullOnDelete();
            }
            if (!Schema::hasColumn('fixed_itineraries', 'duration_days')) {
                $table->integer('duration_days')->default(1)->after('title');
            }
            if (!Schema::hasColumn('fixed_itineraries', 'duration_nights')) {
                $table->integer('duration_nights')->default(0)->after('duration_days');
            }
            if (!Schema::hasColumn('fixed_itineraries', 'short_description')) {
                $table->text('short_description')->nullable()->after('duration_nights');
            }
            if (!Schema::hasColumn('fixed_itineraries', 'highlights')) {
                $table->json('highlights')->nullable()->after('itinerary_description');
            }
            if (!Schema::hasColumn('fixed_itineraries', 'inclusions')) {
                $table->json('inclusions')->nullable()->after('highlights');
            }
            if (!Schema::hasColumn('fixed_itineraries', 'exclusions')) {
                $table->json('exclusions')->nullable()->after('inclusions');
            }
            if (!Schema::hasColumn('fixed_itineraries', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->after('exclusions');
            }
            if (!Schema::hasColumn('fixed_itineraries', 'days')) {
                $table->json('days')->nullable()->after('terms_conditions');
            }
            if (!Schema::hasColumn('fixed_itineraries', 'image')) {
                $table->string('image')->nullable()->after('currency');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fixed_itineraries', function (Blueprint $table) {
            $cols = ['destination_id', 'duration_days', 'duration_nights', 'short_description', 'highlights', 'inclusions', 'exclusions', 'terms_conditions', 'days', 'image'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('fixed_itineraries', $col)) {
                    if ($col === 'destination_id') {
                        $table->dropForeign(['destination_id']);
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
}
