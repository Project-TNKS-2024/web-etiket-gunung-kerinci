<?php

use App\Http\Controllers\API\SOSController;
use App\Http\Controllers\API\SOSChatController;
use App\Http\Controllers\API\SOSCallController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('sos')->group(function () {
    // SOS Panic Button
    Route::post('/trigger', [SOSController::class, 'trigger']);
    Route::get('/active', [SOSController::class, 'active']);

    // SOS Chat
    Route::post('/chat/{sos_id}/send', [SOSChatController::class, 'send']);
    Route::get('/chat/{sos_id}/messages', [SOSChatController::class, 'messages']);

    // Call Options
    Route::get('/call-options', [SOSCallController::class, 'callOptions']);

    // Disaster Reports
    Route::post('/disaster-report', [SOSCallController::class, 'disasterReport']);
    Route::get('/disaster-reports', [SOSCallController::class, 'myReports']);
});
