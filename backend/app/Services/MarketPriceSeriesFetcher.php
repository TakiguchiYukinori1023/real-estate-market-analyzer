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
     * @param int $stationId
     * @param string $propertyType
     * @param string $floorAreaBand
     * @param string $builtYearBand
     * @return Collection<int, MarketPriceSeries>
     */
    public function fetch(
        int $stationId,
        string $propertyType,
        string $floorAreaBand,
        string $builtYearBand,
    ): Collection {
        return MarketPriceSeries::query()
            ->where('station_id', $stationId)
            ->where('property_type', $propertyType)
            ->where('floor_area_band', $floorAreaBand)
            ->where('built_year_band', $builtYearBand)
            ->orderBy('target_month')
            ->get();
    }
}