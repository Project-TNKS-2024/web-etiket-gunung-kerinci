<?php

use App\Http\Controllers\API\TrackingController;
use App\Http\Controllers\API\CheckpointController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('tracking')->group(function () {
    // GPS tracking
    Route::post('/gps', [TrackingController::class, 'store']);
    Route::post('/gps/batch', [TrackingController::class, 'storeBatch']);
    Route::get('/my-position', [TrackingController::class, 'myPosition']);

    // Checkpoint / trail tracking
    Route::post('/checkpoint/qr', [CheckpointController::class, 'scanQr']);
    Route::post('/checkpoint/gps', [CheckpointController::class, 'detectGps']);
    Route::post('/checkpoint/manual', [CheckpointController::class, 'manualCheckin']);
    Route::get('/progress/{booking_id}', [CheckpointController::class, 'progress']);
    Route::get('/posts/{gate_id}', [CheckpointController::class, 'posts']);
});
