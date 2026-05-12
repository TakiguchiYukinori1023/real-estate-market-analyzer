<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
{
    /**
     * APIレスポンス用に駅データを整形する
     *
     * @param Request $request リクエスト
     * @return array<string, mixed> レスポンス配列
     */
    public function toArray(Request $request): array
    {
      return [
          'id' => $this->id,
          'display_name' => "{$this->line_name} {$this->station_name}",
      ];
    }
}