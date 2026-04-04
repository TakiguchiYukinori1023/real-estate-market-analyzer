<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\PropertyPriceObservation;

class PropertyPriceObservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PropertyPriceObservation::query()->delete();

        $mansion = Property::where('property_type', 'mansion')->firstOrFail();
        $house = Property::where('property_type', 'house')->firstOrFail();

        PropertyPriceObservation::create([
            'property_id' => $mansion->id,
            'observed_on' => '2026-03-01',
            'price_yen' => 59800000,
        ]);

        PropertyPriceObservation::create([
            'property_id' => $mansion->id,
            'observed_on' => '2026-04-01',
            'price_yen' => 58800000,
        ]);

        PropertyPriceObservation::create([
            'property_id' => $house->id,
            'observed_on' => '2026-04-01',
            'price_yen' => 69800000,
        ]);
    }
}
