<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\DestinasiUser;
use App\Models\GkSos;

// Default user channel
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Emergency channel: admin assigned to destinasi OR active hiker at destinasi
Broadcast::channel('emergency.{destinasiId}', function ($user, $destinasiId) {
    if ($user->role === 'admin' || $user->role === 'superadmin') {
        return DestinasiUser::where('user_id', $user->id)
            ->where('destinasi_id', $destinasiId)
            ->exists();
    }
    return $user->hasActiveBookingAt((int) $destinasiId);
}, ['guards' => ['web', 'sanctum']]);

// SOS admin channel: only admins assigned to destinasi
Broadcast::channel('sos.{destinasiId}', function ($user, $destinasiId) {
    return in_array($user->role, ['admin', 'superadmin'])
        && DestinasiUser::where('user_id', $user->id)
            ->where('destinasi_id', $destinasiId)
            ->exists();
}, ['guards' => ['web', 'sanctum']]);

// SOS Chat: the hiker who triggered OR assigned admin
Broadcast::channel('sos-chat.{sosId}', function ($user, $sosId) {
    $sos = GkSos::find($sosId);
    if (!$sos) return false;

    if (in_array($user->role, ['admin', 'superadmin'])) {
        return DestinasiUser::where('user_id', $user->id)
            ->where('destinasi_id', $sos->id_destinasi)
            ->exists();
    }

    return $sos->pendaki && $sos->pendaki->booking
        && $sos->pendaki->booking->id_user === $user->id;
}, ['guards' => ['web', 'sanctum']]);

// Tracking channel: admin only
Broadcast::channel('tracking.{destinasiId}', function ($user, $destinasiId) {
    return in_array($user->role, ['admin', 'superadmin'])
        && DestinasiUser::where('user_id', $user->id)
            ->where('destinasi_id', $destinasiId)
            ->exists();
}, ['guards' => ['web', 'sanctum']]);
