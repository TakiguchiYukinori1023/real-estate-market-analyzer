<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MarketPriceSeriesResource extends JsonResource
{
    /**
     * リソースを配列に変換する
     *
     * @param Request $request リクエスト
     * @return array<string, mixed> レスポンス配列
     */
    public function toArray($request): array
    {
        $targetMonth = $this->target_month;

        return [
            'target_month' => $targetMonth instanceof CarbonInterface
                ? $targetMonth->format('Y-m')
                : (string) $targetMonth,
            'floor_area_band' => $this->floor_area_band,
            'built_year_band' => $this->built_year_band,
            'median_price_per_sqm' => (int) $this->median_price_per_sqm,
            'p25_price_per_sqm' => (int) $this->p25_price_per_sqm,
            'p75_price_per_sqm' => (int) $this->p75_price_per_sqm,
            'sample_count' => (int) $this->sample_count,
        ];
    }
}