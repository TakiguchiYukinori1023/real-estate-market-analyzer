<?php

namespace App\Services;

use App\Models\MarketPriceSeries;
use App\Models\PropertyPriceObservation;
use App\Services\MarketPrice\BandResolver;
use Illuminate\Support\Collection;

class MarketPriceAggregator
{
    public function __construct(
        private readonly BandResolver $bandResolver,
    ) {
    }

    /**
     * 月次相場集計を実行し、作成件数を返す
     *
     * @return int 作成件数
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
            ->whereHas('property', function ($query): void {
                $query
                    ->whereNotNull('floor_area_sqm')
                    ->whereNotNull('built_year');
            })
            ->get();
    }

    /**
     * グルーピングキーを生成する
     *
     * @param PropertyPriceObservation $obsservation 観測データ
     * @return string グルーピングキー
     */
    private function buildGroupKey(PropertyPriceObservation $observation): string
    {
        $property = $observation->property;

        $floorAreaBand = $this->bandResolver->resolveFloorAreaBand((int) $property->floor_area_sqm);
        $builtYearBand = $this->bandResolver->resolveBuiltYearBand((int) $property->built_year);

        $targetMonth = $observation->observed_on
            ->copy()
            ->startOfMonth()
            ->format('Y-m-d');

        return implode('|', [
            $property->station_id,
            $property->property_type,
            $floorAreaBand,
            $builtYearBand,
            $targetMonth,
        ]);
    }

    /**
     * グループ内の㎡単価一覧を抽出する
     *
     * @param Collection<int, PropertyPriceObservation> $groupedItems
     * @return Collection<int, int> ㎡単価一覧
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
     * @param Collection<int, PropertyPriceObservation> $groupedItems グループ化された観測データ
     * @param Collection<int, int> $pricePerSqmList ㎡単価一覧
     * @return void
     */
    private function createMarketPriceSeries(Collection $groupedItems, Collection $pricePerSqmList): void
    {
        $firstObservation = $groupedItems->first();
        $property = $firstObservation->property;

        $floorAreaBand = $this->bandResolver->resolveFloorAreaBand((int) $property->floor_area_sqm);
        $builtYearBand = $this->bandResolver->resolveBuiltYearBand((int) $property->built_year);

        $targetMonth = $firstObservation->observed_on
            ->copy()
            ->startOfMonth()
            ->format('Y-m-d');

        MarketPriceSeries::create([
            'station_id' => $property->station_id,
            'property_type' => $property->property_type,
            'floor_area_band' => $floorAreaBand,
            'built_year_band' => $builtYearBand,
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
     * @param Collection<int, int> $sortedValues 昇順に並んだ数値一覧
     * @pram float $percentile パーセンタイル
     * @return int パーセンタイル値
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