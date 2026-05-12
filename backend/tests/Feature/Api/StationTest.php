<?php

namespace Tests\Feature\Api;

use App\Models\Station;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StationTest extends TestCase
{
    use RefreshDatabase;

    public function test_駅一覧を取得できる(): void
    {
        Station::query()->create([
            'line_name' => '東横線',
            'station_name' => '横浜',
            'display_name' => '東横線 横浜',
        ]);

        $response = $this->getJson('/api/stations');

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => 1,
                        'display_name' => '東横線 横浜',
                    ],
                ],
            ]);
    }

    public function test_駅一覧は路線名と駅名の昇順で取得できる(): void
    {
        Station::query()->create([
            'line_name' => '東横線',
            'station_name' => '横浜',
            'display_name' => '東横線 横浜',
        ]);

        Station::query()->create([
            'line_name' => '東横線',
            'station_name' => '渋谷',
            'display_name' => '東横線 渋谷',
        ]);

        Station::query()->create([
            'line_name' => 'みなとみらい線',
            'station_name' => '元町・中華街',
            'display_name' => 'みなとみらい線 元町・中華街',
        ]);

        $response = $this->getJson('/api/stations');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.display_name', 'みなとみらい線 元町・中華街')
            ->assertJsonPath('data.1.display_name', '東横線 横浜')
            ->assertJsonPath('data.2.display_name', '東横線 渋谷');
    }

    public function test_駅が存在しない場合は空配列を返す(): void
    {
        $response = $this->getJson('/api/stations');

        $response
            ->assertOk()
            ->assertJson([
                'data' => [],
            ]);
    }
}