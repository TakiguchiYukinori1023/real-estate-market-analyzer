<?php

namespace App\Services\MarketPrice;

class BandResolver
{
    /**
     * 床面積から床面積帯を判定する
     *
     * @param int $floorArea 床面積（㎡）
     * @return string 床面積帯
     */
    public function resolveFloorAreaBand(int $floorArea): string
    {
        if ($floorArea < 30) {
            return 'under30';
        }

        if ($floorArea < 50) {
            return '30_50';
        }

        if ($floorArea < 70) {
            return '50_70';
        }

        if ($floorArea < 90) {
            return '70_90';
        }

        if ($floorArea < 120) {
            return '90_120';
        }

        return 'over120';
    }

    /**
     * 建築年から築年帯を判定する
     *
     * @param int $builtYear 建築年
     * @param int|null $currentYear 判定基準年。未指定の場合は現在年を使用する。
     * @return string 築年帯
     */
    public function resolveBuiltYearBand(int $builtYear, ?int $currentYear = null): string
    {
        $currentYear ??= now()->year;

        $buildingAge = $currentYear - $builtYear;

        if ($buildingAge <= 5) {
            return '0_5';
        }

        if ($buildingAge <= 10) {
            return '6_10';
        }

        if ($buildingAge <= 20) {
            return '11_20';
        }

        if ($buildingAge <= 30) {
            return '21_30';
        }

        return 'over31';
    }
}