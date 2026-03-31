<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'station_id',
        'property_type',
        'floor_area_sqm',
        'built_year',
        'walk_minutes',
        'building_structure',
        'has_parking',
        'is_new_build',
        'management_fee_yen',
    ];

    /**
     * 物件が所属する駅
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    /**
     * 物件に紐づく価格観測履歴
     */
    public function priceObservations(): HasMany
    {
        return $this->hasMany(PropertyPriceObservation::class);
    }
}
