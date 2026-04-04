<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Station;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Property::query()->delete();

        $motomachi = Station::where('station_name', '元町・中華街')->firstOrFail();
        $yamate = Station::where('station_name', '山手')->firstOrFail();

        Property::create([
            'station_id' => $motomachi->id,
            'property_type' => 'mansion',
            'floor_area_sqm' => 68.50,
            'built_year' => 2018,
            'walk_minutes' => 9,
            'building_structure' => 'rc',
            'has_parking' => false,
            'is_new_build' => false,
            'management_fee_yen' => 18000,
        ]);

        Property::create([
            'station_id' => $yamate->id,
            'property_type' => 'house',
            'floor_area_sqm' => 103.20,
            'built_year' => 2024,
            'walk_minutes' => 18,
            'building_structure' => 'wood',
            'has_parking' => true,
            'is_new_build' => true,
            'management_fee_yen' => null,
        ]);
    }
}
