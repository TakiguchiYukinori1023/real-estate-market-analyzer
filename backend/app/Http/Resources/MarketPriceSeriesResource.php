<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MarketPriceSeriesResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $targetMonth = $this->target_month;

        return [
            'target_month' => $targetMonth instanceof CarbonInterface
                ? $targetMonth->format('Y-m')
                : (string) $targetMonth,
            'median_price_per_sqm' => (int) $this->median_price_per_sqm,
            'p25_price_per_sqm' => (int) $this->p25_price_per_sqm,
            'p75_price_per_sqm' => (int) $this->p75_price_per_sqm,
            'sample_count' => (int) $this->sample_count,
        ];
    }
}