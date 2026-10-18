<?php

use App\Http\Controllers\API\CheckpointV2Controller;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('tracking/v2')->group(function () {
    Route::get('/posts/{gate_id}', [CheckpointV2Controller::class, 'posts']);
    Route::post('/checkpoint/qr', [CheckpointV2Controller::class, 'scanQr']);
    Route::post('/checkpoint/manual', [CheckpointV2Controller::class, 'manualCheckin']);
    Route::post('/member-note', [CheckpointV2Controller::class, 'saveMemberNote']);
    Route::get('/progress/{booking_id}', [CheckpointV2Controller::class, 'progress']);
    Route::delete('/checkpoint/{checkpoint_log_id}', [CheckpointV2Controller::class, 'cancelCheckin']);
});
