<?php

use App\Http\Controllers\API\EmergencyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('emergency')->group(function () {
    Route::post('/trigger', [EmergencyController::class, 'trigger']);
    Route::get('/active', [EmergencyController::class, 'active']);
});
