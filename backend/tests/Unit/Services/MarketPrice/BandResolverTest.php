<?php

namespace Tests\Unit\Services\MarketPrice;

use App\Services\MarketPrice\BandResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BandResolverTest extends TestCase
{
    #[DataProvider('floorAreaBandProvider')]
    public function test_resolve_floor_area_band(int $floorArea, string $expected): void
    {
        $resolver = new BandResolver();

        $actual = $resolver->resolveFloorAreaBand($floorArea);

        $this->assertSame($expected, $actual);
    }

    public static function floorAreaBandProvider(): array
    {
        return [
            '29 sqm' => [29, 'under30'],
            '30 sqm' => [30, '30_50'],
            '49 sqm' => [49, '30_50'],
            '50 sqm' => [50, '50_70'],
            '69 sqm' => [69, '50_70'],
            '70 sqm' => [70, '70_90'],
            '89 sqm' => [89, '70_90'],
            '90 sqm' => [90, '90_120'],
            '119 sqm' => [119, '90_120'],
            '120_sqm' => [120, 'over120'],
        ];
    }

    #[DataProvider('builtYearBandProvider')]
    public function test_resolve_built_year_band(int $builtYear, int $currentYear, string $expected): void
    {
        $resolver = new BandResolver();

        $actual = $resolver->resolveBuiltYearBand($builtYear, $currentYear);

        $this->assertSame($expected, $actual);
    }

    public static function builtYearBandProvider(): array
    {
        return [
            'age 0' => [2025, 2025, '0_5'],
            'age 5' => [2020, 2025, '0_5'],
            'age 6' => [2019, 2025, '6_10'],
            'age 10' => [2015, 2025, '6_10'],
            'age 11' => [2014, 2025, '11_20'],
            'age 20' => [2005, 2025, '11_20'],
            'age 21' => [2004, 2025, '21_30'],
            'age 30' => [1995, 2025, '21_30'],
            'age 31' => [1994, 2025, 'over31'],
        ];
    }
}