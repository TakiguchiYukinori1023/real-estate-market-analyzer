<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\MarketPriceSeries;
use App\Models\Station;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MarketPriceSeriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_正しい条件で集計済み相場データ一覧を取得できる(): void
    {
        $station = Station::query()->create([
            'line_name' => '東横線',
            'station_name' => '横浜',
            'display_name' => '東横線 横浜',
        ]);

        MarketPriceSeries::query()->create([
            'station_id' => $station->id,
            'property_type' => 'mansion',
            'floor_area_band' => '50_70',
            'built_year_band' => '11_20',
            'target_month' => '2025-01-01',
            'median_price_per_sqm' => 820000,
            'p25_price_per_sqm' => 760000,
            'p75_price_per_sqm' => 900000,
            'sample_count' => 18,
        ]);

        $response = $this->getJson('/api/market-price-series?' . http_build_query([
            'station_id' => $station->id,
            'property_type' => 'mansion',
        ]));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'target_month' => '2025-01',
                        'median_price_per_sqm' => 820000,
                        'p25_price_per_sqm' => 760000,
                        'p75_price_per_sqm' => 900000,
                        'sample_count' => 18,
                    ],
                ],
            ]);
    }

    public function test_station_id未指定の場合はバリデーションエラーになる(): void
    {
        $response = $this->getJson('/api/market-price-series?' . http_build_query([
            'property_type' => 'mansion',
        ]));

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['station_id']);
    }

    public function test_property_typeが不正な場合はバリデーションエラーになる(): void
    {
        $station = Station::query()->create([
            'line_name' => '東横線',
            'station_name' => '横浜',
            'display_name' => '東横線 横浜',
        ]);

        $response = $this->getJson('/api/market-price-series?' . http_build_query([
            'station_id' => $station->id,
            'property_type' => 'invalid',
        ]));

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['property_type']);
    }

    public function test_station_idが存在しない場合はバリデーションエラーになる(): void
    {
        $response = $this->getJson(
            '/api/market-price-series?' . http_build_query([
                'station_id' => 999999,
                'property_type' => 'mansion',
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['station_id']);
    }

    public function test_該当するデータがない場合は空配列を返す(): void
    {
        $station = Station::query()->create([
            'line_name' => '東横線',
            'station_name' => '横浜',
            'display_name' => '東横線 横浜',
        ]);

        $response = $this->getJson(
            '/api/market-price-series?' . http_build_query([
                'station_id' => $station->id,
                'property_type' => 'mansion',
            ])
        );

        $response
            ->assertOk()
            ->assertJson([
                'data' => [],
            ]);
    }

    public function test_target_monthの昇順で集計済み相場データ一覧を取得できる(): void
    {
        $station = Station::query()->create([
            'line_name' => '東横線',
            'station_name' => '横浜',
            'display_name' => '東横線 横浜',
        ]);

        MarketPriceSeries::query()->create([
            'station_id' => $station->id,
            'property_type' => 'mansion',
            'floor_area_band' => '50_70',
            'built_year_band' => '11_20',
            'target_month' => '2025-03-01',
            'median_price_per_sqm' => 860000,
            'p25_price_per_sqm' => 800000,
            'p75_price_per_sqm' => 930000,
            'sample_count' => 20,
        ]);

        MarketPriceSeries::query()->create([
            'station_id' => $station->id,
            'property_type' => 'mansion',
            'floor_area_band' => '50_70',
            'built_year_band' => '11_20',
            'target_month' => '2025-01-01',
            'median_price_per_sqm' => 820000,
            'p25_price_per_sqm' => 760000,
            'p75_price_per_sqm' => 900000,
            'sample_count' => 18,
        ]);

        MarketPriceSeries::query()->create([
            'station_id' => $station->id,
            'property_type' => 'mansion',
            'floor_area_band' => '50_70',
            'built_year_band' => '11_20',
            'target_month' => '2025-02-01',
            'median_price_per_sqm' => 840000,
            'p25_price_per_sqm' => 780000,
            'p75_price_per_sqm' => 910000,
            'sample_count' => 19,
        ]);

        $response = $this->getJson(
            '/api/market-price-series?' . http_build_query([
                'station_id' => $station->id,
                'property_type' => 'mansion',
            ])
        );

        $response
            ->assertOk()
            ->assertJsonPath('data.0.target_month', '2025-01')
            ->assertJsonPath('data.1.target_month', '2025-02')
            ->assertJsonPath('data.2.target_month', '2025-03');
    }

    public function test_floor_area_bandを指定した場合は該当する面積帯のみ取得できる(): void
    {
        $station = Station::query()->create([
            'line_name' => '東横線',
            'station_name' => '横浜',
            'display_name' => '東横線 横浜',
        ]);

        MarketPriceSeries::query()->create([
            'station_id' => $station->id,
            'property_type' => 'mansion',
            'floor_area_band' => '30_50',
            'built_year_band' => '11_20',
            'target_month' => '2025-01-01',
            'median_price_per_sqm' => 820000,
            'p25_price_per_sqm' => 760000,
            'p75_price_per_sqm' => 900000,
            'sample_count' => 18,
        ]);

        MarketPriceSeries::query()->create([
            'station_id' => $station->id,
            'property_type' => 'mansion',
            'floor_area_band' => '50_70',
            'built_year_band' => '11_20',
            'target_month' => '2025-01-01',
            'median_price_per_sqm' => 900000,
            'p25_price_per_sqm' => 850000,
            'p75_price_per_sqm' => 950000,
            'sample_count' => 10,
        ]);

        $response = $this->getJson('/api/market-price-series?' . http_build_query([
            'station_id' => $station->id,
            'property_type' => 'mansion',
            'floor_area_band' => '50_70',
        ]));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.median_price_per_sqm', 900000)
            ->assertJsonPath('data.0.sample_count', 10);
    }

    public function test_built_year_bandを指定した場合は該当する築年帯のみ取得できる(): void
    {
        $station = Station::query()->create([
            'line_name' => '東横線',
            'station_name' => '横浜',
            'display_name' => '東横線 横浜',
        ]);

        MarketPriceSeries::query()->create([
            'station_id' => $station->id,
            'property_type' => 'mansion',
            'floor_area_band' => '50_70',
            'built_year_band' => '11_20',
            'target_month' => '2025-01-01',
            'median_price_per_sqm' => 820000,
            'p25_price_per_sqm' => 760000,
            'p75_price_per_sqm' => 900000,
            'sample_count' => 18,
        ]);

        MarketPriceSeries::query()->create([
            'station_id' => $station->id,
            'property_type' => 'mansion',
            'floor_area_band' => '50_70',
            'built_year_band' => '21_30',
            'target_month' => '2025-01-01',
            'median_price_per_sqm' => 900000,
            'p25_price_per_sqm' => 850000,
            'p75_price_per_sqm' => 950000,
            'sample_count' => 10,
        ]);

        $response = $this->getJson('/api/market-price-series?' . http_build_query([
            'station_id' => $station->id,
            'property_type' => 'mansion',
            'built_year_band' => '21_30',
        ]));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.median_price_per_sqm', 900000)
            ->assertJsonPath('data.0.sample_count', 10);
    }
}