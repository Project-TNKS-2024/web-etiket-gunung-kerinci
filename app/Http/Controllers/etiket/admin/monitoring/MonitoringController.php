<?php

namespace App\Http\Controllers\etiket\admin\monitoring;

use App\Http\Controllers\Controller;
use App\Models\GkTracking;
use App\Models\GkSos;
use App\Models\GkEmergencyMessage;
use App\Models\gk_booking;
use App\Models\GkPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    /**
     * Live monitoring dashboard.
     * GET /admin/monitoring
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $destinasiIds = $user->destinasis()->pluck('destinasis.id');

        // Active hikers (status_booking = 6) at admin's destinasis
        $activeBookings = gk_booking::where('status_booking', 6)
            ->whereHas('gktiket', fn($q) => $q->whereIn('id_destinasi', $destinasiIds))
            ->with(['pendakis.biodata', 'gateMasuk', 'destinasi'])
            ->get();

        // Get last known position for each active pendaki
        $pendakiIds = $activeBookings->flatMap(fn($b) => $b->pendakis->pluck('id'));
        $lastPositions = GkTracking::whereIn('id_pendaki', $pendakiIds)
            ->select('id_pendaki', 'latitude', 'longitude', 'altitude', 'battery_level', 'recorded_at')
            ->whereIn('id', function ($q) use ($pendakiIds) {
                $q->select(DB::raw('MAX(id)'))
                    ->from('gk_tracking')
                    ->whereIn('id_pendaki', $pendakiIds)
                    ->groupBy('id_pendaki');
            })
            ->get()
            ->keyBy('id_pendaki');

        // Active SOS
        $activeSos = GkSos::whereIn('id_destinasi', $destinasiIds)
            ->whereIn('status', ['active', 'acknowledged', 'dispatched'])
            ->with('pendaki.biodata')
            ->get();

        // Active emergencies
        $activeEmergencies = GkEmergencyMessage::whereIn('id_destinasi', $destinasiIds)
            ->where('status', 'active')
            ->count();

        // Posts for map
        $posts = GkPost::whereHas('gate', fn($q) => $q->whereIn('id_destinasi', $destinasiIds))
            ->where('status', true)
            ->get();

        return view('etiket.admin.monitoring.index', compact(
            'activeBookings', 'lastPositions', 'activeSos',
            'activeEmergencies', 'posts', 'destinasiIds'
        ));
    }
}
