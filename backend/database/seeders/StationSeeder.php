<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Station;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Station::query()->delete();

        Station::create([
            'line_name' => 'みなとみらい線',
            'station_name' => '元町・中華街',
            'display_name' => 'みなとみらい線 元町・中華街',
        ]);

        Station::create([
            'line_name' => 'JR根岸線',
            'station_name' => '山手',
            'display_name' => 'JR根岸線 山手',
        ]);
    }
}
