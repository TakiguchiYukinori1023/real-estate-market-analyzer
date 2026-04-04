<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketPriceSeries;
use App\Models\Station;

class MarketPriceSeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MarketPriceSeries::query()->delete();

        $motomachi = Station::where('station_name', '元町・中華街')->firstOrFail();
        $yamate = Station::where('station_name', '山手')->firstOrFail();

        MarketPriceSeries::create([
            'station_id' => $motomachi->id,
            'property_type' => 'mansion',
            'floor_area_band' => '60-80',
            'built_year_band' => '2010-2019',
            'target_month' => '2026-04-01',
            'median_price_per_sqm' => 860000,
            'p25_price_per_sqm' => 820000,
            'p75_price_per_sqm' => 890000,
            'sample_count' => 12,
        ]);

        MarketPriceSeries::create([
            'station_id' => $yamate->id,
            'property_type' => 'house',
            'floor_area_band' => '100-120',
            'built_year_band' => '2020-2029',
            'target_month' => '2026-04-01',
            'median_price_per_sqm' => 680000,
            'p25_price_per_sqm' => 650000,
            'p75_price_per_sqm' => 710000,
            'sample_count' => 8,
        ]);
    }
}
