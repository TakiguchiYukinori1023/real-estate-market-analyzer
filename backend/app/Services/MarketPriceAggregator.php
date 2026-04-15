<?php

namespace App\Services;

use App\Models\MarketPriceSeries;
use App\Models\PropertyPriceObservation;
use Illuminate\Support\Collection;

class MarketPriceAggregator
{
    private const DEFAULT_FLLOR_AREA_BAND = 'all';
    private const DEFAULT_BUILT_YEAR_BAND = 'all';

    /**
     **月次相場集計を実行し、作成件数を返す
     */
    public function execute(): int
    {
        MarketPriceSeries::query()->delete();

        $observations = $this->loadObservations();

        if ($observations->isEmpty()) {
            return 0;
        }

        $groupedObservations = $observations->groupBy(
            fn (PropertyPriceObservation $observation): string => $this->buildGroupKey($observation)
        );

        $createdCount = 0;

        foreach ($groupedObservations as $groupedItems) {
            $pricePerSqmList = $this->extractPricePerSqmList($groupedItems);

            if ($pricePerSqmList->isEmpty()) {
                continue;
            }

            $this->createMarketPriceSeries($groupedItems, $pricePerSqmList);
            $createdCount++;
        }

        return $createdCount;
    }

    /**
     * 集計対象の観測データを取得する
     *
     * @return Collection<int, PropertyPriceObservation>
     */
    private function loadObservations(): Collection
    {
        return PropertyPriceObservation::query()
            ->with('property')
            ->whereHas('property')
            ->get();
    }

    /**
     * グルーピングキーを生成する
     */
    private function buildGroupKey(PropertyPriceObservation $observation): string
    {
        $targetMonth = $observation->observed_on
            ->copy()
            ->startOfMonth()
            ->format('Y-m-d');

        return implode('|', [
            $observation->property->station_id,
            $observation->property->property_type,
            $targetMonth,
        ]);
    }

    /**
     * グループ内の㎡単価一覧を抽出する
     *
     * @param Collection<int, PropertyPriceObservation> $groupedItems
     * @return Collection<int, int>
     */
    private function extractPricePerSqmList(Collection $groupedItems): Collection
    {
        return $groupedItems
            ->pluck('price_per_sqm')
            ->filter(fn ($value): bool => $value !== null)
            ->map(fn ($value): int => (int) $value)
            ->sort()
            ->values();
    }

    /**
     * 集計結果を market_price_series に保存する
     *
     * @param Collection<int, PropertyPriceObservation> $groupedItems
     * @param Collection<int, int> $pricePerSqmList
     */
    private function createMarketPriceSeries(Collection $groupedItems, Collection $pricePerSqmList): void
    {
        $firstObservation = $groupedItems->first();

        $stationId = $firstObservation->property->station_id;
        $propertyType = $firstObservation->property->property_type;
        $targetMonth = $firstObservation->observed_on
            ->copy()
            ->startOfMonth()
            ->format('Y-m-d');

        MarketPriceSeries::create([
            'station_id' => $stationId,
            'property_type' => $propertyType,
            'floor_area_band' => self::DEFAULT_FLLOR_AREA_BAND,
            'built_year_band' => self::DEFAULT_BUILT_YEAR_BAND,
            'target_month' => $targetMonth,
            'median_price_per_sqm' => $this->percentile($pricePerSqmList, 0.5),
            'p25_price_per_sqm' => $this->percentile($pricePerSqmList, 0.25),
            'p75_price_per_sqm' => $this->percentile($pricePerSqmList, 0.75),
            'sample_count' => $pricePerSqmList->count(),
        ]);
    }

    /**
     * パーセンタイル値を整数で返す
     *
     * Phase1ではシンプルに nearest-rank に近い方法で算出する。
     *
     * @param \Illuminate\Support\Collection<int, int> $sortedValues
     */
    private function percentile(Collection $sortedValues, float $percentile): int
    {
        $count = $sortedValues->count();

        if ($count === 0) {
            throw new \InvalidArgumentException('sortedValues must not be empty.');
        }

        if ($count === 1) {
            return (int) $sortedValues->first();
        }

        $index = (int) round(($count - 1) * $percentile);

        return (int) $sortedValues->get($index);
    }
}