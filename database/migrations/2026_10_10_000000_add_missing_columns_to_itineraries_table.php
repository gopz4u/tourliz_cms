<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itineraries', function (Blueprint $table) {
            if (!Schema::hasColumn('itineraries', 'title')) {
                $table->string('title')->after('id');
            }
            if (!Schema::hasColumn('itineraries', 'slug')) {
                $table->string('slug')->unique()->after('title');
            }
            if (!Schema::hasColumn('itineraries', 'itinerary_type')) {
                $table->enum('itinerary_type', ['b2c', 'b2b'])->default('b2c')->index()->after('slug');
            }
            if (!Schema::hasColumn('itineraries', 'destination_id')) {
                $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete()->after('itinerary_type');
            }
            if (!Schema::hasColumn('itineraries', 'duration_days')) {
                $table->integer('duration_days')->default(1)->after('destination_id');
            }
            if (!Schema::hasColumn('itineraries', 'duration_nights')) {
                $table->integer('duration_nights')->default(0)->after('duration_days');
            }
            if (!Schema::hasColumn('itineraries', 'description')) {
                $table->text('description')->nullable()->after('duration_nights');
            }
            if (!Schema::hasColumn('itineraries', 'highlights')) {
                $table->json('highlights')->nullable()->after('description');
            }
            if (!Schema::hasColumn('itineraries', 'inclusions')) {
                $table->json('inclusions')->nullable()->after('highlights');
            }
            if (!Schema::hasColumn('itineraries', 'exclusions')) {
                $table->json('exclusions')->nullable()->after('inclusions');
            }
            if (!Schema::hasColumn('itineraries', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->after('exclusions');
            }
            if (!Schema::hasColumn('itineraries', 'status')) {
                $table->string('status')->default('draft')->index()->after('terms_conditions');
            }
            if (!Schema::hasColumn('itineraries', 'currency')) {
                $table->string('currency', 10)->default('MYR')->after('status');
            }
            if (!Schema::hasColumn('itineraries', 'is_published')) {
                $table->boolean('is_published')->default(false)->index()->after('status');
            }
            if (!Schema::hasColumn('itineraries', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete()->after('is_published');
            }
            if (!Schema::hasColumn('itineraries', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete()->after('created_by');
            }
            if (!Schema::hasColumn('itineraries', 'deleted_at')) {
                $table->softDeletes();
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
        Schema::table('itineraries', function (Blueprint $table) {
            if (Schema::hasColumn('itineraries', 'itinerary_type')) {
                $table->dropColumn('itinerary_type');
            }
        });
    }
}
