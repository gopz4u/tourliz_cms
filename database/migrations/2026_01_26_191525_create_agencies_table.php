<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Cleanup previous approach if exists
        Schema::dropIfExists('agency_places');
        Schema::dropIfExists('agency_profiles');

        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('primary_contact_name')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->decimal('default_markup', 5, 2)->default(10.00);
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Re-create agency_places pivot linked to agencies table
        Schema::create('agency_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies')->onDelete('cascade');
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->boolean('is_specialist')->default(false);
            $table->timestamps();

            $table->unique(['agency_id', 'place_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agencies');
    }
}
