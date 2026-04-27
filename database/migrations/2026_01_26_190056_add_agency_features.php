<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgencyFeatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add role to users if not exists
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('user')->after('email')->comment('admin, agency, user');
                $table->string('phone')->nullable()->after('role');
                $table->string('company_name')->nullable()->after('phone');
            });
        }

        // Create agency profiles table
        Schema::create('agency_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('logo')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('license_number')->nullable();
            $table->decimal('default_markup', 5, 2)->default(10.00);
            $table->string('currency', 3)->default('USD');
            $table->string('primary_contact_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create agency_places table (pivot)
        Schema::create('agency_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_profile_id')->constrained('agency_profiles')->onDelete('cascade');
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->boolean('is_specialist')->default(false);
            $table->timestamps();

            $table->unique(['agency_profile_id', 'place_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('agency_places');
        Schema::dropIfExists('agency_profiles');

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['role', 'phone', 'company_name']);
            });
        }
    }
}
