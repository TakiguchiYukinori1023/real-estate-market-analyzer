<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    protected $fillable = [
        'line_name',
        'station_name',
        'display_name',
    ];

    /**
     * 駅に紐づく物件一覧
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * 駅に紐づく月次相場集計一覧
     */
    public function marketPriceSeries(): HasMany
    {
        return $this->hasMany(MarketPriceSeries::class);
    }
}
