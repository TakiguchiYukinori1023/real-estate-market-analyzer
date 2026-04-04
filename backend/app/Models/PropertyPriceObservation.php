<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyPriceObservation extends Model
{
    protected $fillable = [
        'property_id',
        'observed_on',
        'price_yen',
        'price_per_sqm',
    ];

    protected $casts = [
        'observed_on' => 'date',
    ];

    /**
     * boot メソッドで保存前処理を定義
     */
    protected static function booted(): void
    {
        static::saving(function (PropertyPriceObservation $model) {

            // property がない場合は何もしない
            if (!$model->property) {
                return;
            }

            // 面積がない or 0 の場合は計算しない
            if (!$model->property->floor_area_sqm || $model->property->floor_area_sqm === 0) {
                return;
            }

            // ㎡単価を計算
            $model->price_per_sqm = (int) round(
                $model->price_yen / $model->property->floor_area_sqm
            );
        });
    }

    /**
     * この観測データが属する物件
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
