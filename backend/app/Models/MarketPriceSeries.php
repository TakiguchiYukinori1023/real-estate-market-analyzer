<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketPriceSeries extends Model
{
    protected $fillable = [
        'station_id',
        'property_type',
        'floor_area_band',
        'built_year_band',
        'target_month',
        'median_price_per_sqm',
        'p25_price_per_sqm',
        'p75_price_per_sqm',
        'sample_count',
    ];

    protected $casts = [
        'target_month' => 'date',
    ];

    /**
     * この相場系列データが属する駅
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
