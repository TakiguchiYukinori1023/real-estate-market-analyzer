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
     * この観測データが属する物件
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
