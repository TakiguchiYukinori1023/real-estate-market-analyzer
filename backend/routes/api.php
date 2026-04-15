<?php

declare(strict_types=1);;

use App\Http\Controllers\Api\MarketPriceSeriesController;
use Illuminate\Support\Facades\Route;

Route::get('/market-price-series', [MarketPriceSeriesController::class, 'index']);