<?php

namespace App\Http\Controllers\etiket\admin\sos;

use App\Http\Controllers\Controller;
use App\Models\GkEmergencyMessage;
use App\Models\GkSos;
use App\Models\GkSosChat;
use Illuminate\Http\Request;

class SafetySummaryController extends Controller
{
    public function __invoke(Request $request)
    {
        $ids = $request->user()->destinasis()->pluck('destinasis.id');
        $active = GkSos::whereIn('id_destinasi', $ids)
            ->whereIn('status', ['active', 'acknowledged', 'dispatched']);

        return response()->json([
            'sos_count' => (clone $active)->count(),
            'emergency_count' => GkEmergencyMessage::whereIn('id_destinasi', $ids)
                ->where('status', 'active')->count(),
            'hiker_messages' => GkSosChat::whereIn('id_sos', (clone $active)->select('id'))
                ->where('sender_type', 'hiker')->count(),
        ])->header('Cache-Control', 'no-store, private');
    }
}
