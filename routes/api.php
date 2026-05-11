<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


// helper
include __DIR__ . '/api/routeAuth.php';
include __DIR__ . '/api/routeUser.php';
include __DIR__ . '/api/routeDomisili.php';
include __DIR__ . '/api/routeTracking.php';
include __DIR__ . '/api/routeEmergency.php';
include __DIR__ . '/api/routeSOS.php';

Route::fallback(function (Request $request) {
   return response()->json([
      'success'  => false,
      'message' => 'API tidak tersedia',
      'errors'  => [
         'path' => $request->path()
      ]
   ], 404);
})->name('api.error');
