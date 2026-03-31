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
        Schema::create('market_price_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('property_type', 20);    // mansion / house
            $table->string('floor_area_band', 20);  // 例: 60-80
            $table->string('built_year_band', 20);  // 例: 2010-2019
            $table->date('target_month');   // その月の先頭日を入れる

            $table->unsignedInteger('median_price_per_sqm');
            $table->unsignedInteger('p25_price_per_sqm');
            $table->unsignedInteger('p75_price_per_sqm');
            $table->unsignedInteger('sample_count');

            $table->timestamps();

            $table->index('station_id');
            $table->index('target_month');
            $table->index(['station_id', 'property_type', 'target_month']);
            $table->unique([
                'station_id',
                'property_type',
                'floor_area_band',
                'built_year_band',
                'target_month',
            ], 'market_price_series_unique_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_price_series');
    }
};
