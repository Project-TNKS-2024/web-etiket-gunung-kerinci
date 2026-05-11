<?php

use App\Http\Controllers\API\TrackingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('tracking')->group(function () {
    Route::post('/gps', [TrackingController::class, 'store']);
    Route::post('/gps/batch', [TrackingController::class, 'storeBatch']);
    Route::get('/my-position', [TrackingController::class, 'myPosition']);
});
