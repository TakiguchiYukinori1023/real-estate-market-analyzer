<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MarketPriceSeries;
use Illuminate\Database\Eloquent\Collection;

/**
 * 集計済み相場データ取得クラス
 */
final class MarketPriceSeriesFetcher
{
    /**
     * 指定条件の集計済み相場データ一覧を取得する
     *
     * @param int $stationId 駅ID
     * @param string $propertyType 物件種別
     * @param string|null $floorAreaBand 床面積帯
     * @param string|null $builtYearBand 築年帯
     * @return Collection<int, MarketPriceSeries> 集計済み相場データ一覧
     */
    public function fetch(
        int $stationId,
        string $propertyType,
        ?string $floorAreaBand,
        ?string $builtYearBand,
    ): Collection {
        $query = MarketPriceSeries::query()
            ->where('station_id', $stationId)
            ->where('property_type', $propertyType);

        if ($floorAreaBand !== null) {
            $query->where('floor_area_band', $floorAreaBand);
        }

        if ($builtYearBand !== null) {
            $query->where('built_year_band', $builtYearBand);
        }

        return $query
            ->orderBy('target_month')
            ->get();
    }
}