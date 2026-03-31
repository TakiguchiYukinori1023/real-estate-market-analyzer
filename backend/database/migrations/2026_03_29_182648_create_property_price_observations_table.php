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
        Schema::create('property_price_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('observed_on');
            $table->unsignedBigInteger('price_yen');
            $table->unsignedInteger('price_per_sqm');

            $table->timestamps();

            $table->index('property_id');
            $table->index('observed_on');
            $table->unique(['property_id', 'observed_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_price_observations');
    }
};
