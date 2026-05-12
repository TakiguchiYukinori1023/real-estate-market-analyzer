<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StationResource;
use App\Models\Station;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * 駅一覧取得APIコントローラー
 */
class StationController extends Controller
{
    /**
     * 駅一覧を取得する
     *
     * @return AnonymousResourceCollection 駅一覧
     */
    public function index(): AnonymousResourceCollection
    {
        // TODO: 日本語として自然な並び順（五十音順）にする
        // 現在はDBの照合順序（collation）に依存した並びになっている
        $stations = Station::query()
            ->orderBy('line_name')
            ->orderBy('station_name')
            ->get();

        return StationResource::collection($stations);
    }
}
