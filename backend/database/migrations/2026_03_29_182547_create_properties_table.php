<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('property_type', 20);    // mansion / house
            $table->decimal('floor_area_sqm',  8, 2);
            $table->unsignedSmallInteger('built_year')->nullable();
            $table->unsignedSmallInteger('walk_minutes')->nullable();
            $table->string('building_structure', 20)->nullable();   // wood / steel / rc
            $table->boolean('has_parking')->default(false);
            $table->boolean('is_new_build')->default(false);
            $table->unsignedInteger('management_fee_yen')->nullable();

            $table->timestamps();

            $table->index('station_id');
            $table->index('property_type');
            $table->index(['station_id', 'property_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
