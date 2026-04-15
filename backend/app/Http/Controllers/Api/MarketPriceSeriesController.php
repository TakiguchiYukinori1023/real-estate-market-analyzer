<?php

declare(strict_type=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MarketPriceSeriesIndexRequest;
use App\Http\Resources\MarketPriceSeriesResource;
use App\Services\MarketPriceSeriesFetcher;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * 集計済み相場データ取得APIコントローラー
 */
final class MarketPriceSeriesController extends Controller
{
    public function __construct(
        private readonly MarketPriceSeriesFetcher $fetcher,
    ) {
    }

    /**
     * 集計済み相場データ一覧を取得する
     *
     * @param MarketPriceSeriesIndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(
        MarketPriceSeriesIndexRequest $request,
    ): AnonymousResourceCollection {
        /** @var array{
         *     station_id:int|string,
         *     property_type:string,
         *     floor_area_band:string,
         *     built_year_band:string
         * } $validated
         */
        $validated = $request->validated();

        $series = $this->fetcher->fetch(
            stationId: (int) $validated['station_id'],
            propertyType: $validated['property_type'],
            floorAreaBand: $validated['floor_area_band'],
            builtYearBand: $validated['built_year_band'],
        );

        return MarketPriceSeriesResource::collection($series);
    }
}
