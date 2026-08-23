<?php

namespace App\Http\Controllers\etiket\admin\emergency;

use App\Http\Controllers\Controller;
use App\Models\GkEmergencyMessage;
use App\Models\destinasi;
use App\Events\EmergencyBroadcast;
use Illuminate\Http\Request;

class EmergencyAdminController extends Controller
{
    /**
     * Emergency dashboard page.
     * GET /admin/emergency
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $destinasiIds = $user->destinasis()->pluck('destinasis.id');

        $emergencies = GkEmergencyMessage::whereIn('id_destinasi', $destinasiIds)
            ->with(['user', 'pendaki.biodata', 'destinasi'])
            ->orderByRaw("FIELD(status, 'active', 'acknowledged', 'resolved')")
            ->orderByDesc('created_at')
            ->paginate(20);

        $activeCount = GkEmergencyMessage::whereIn('id_destinasi', $destinasiIds)
            ->where('status', 'active')->count();

        $destinasis = destinasi::whereIn('id', $destinasiIds)->get();

        return view('etiket.admin.emergency.index', compact('emergencies', 'activeCount', 'destinasis'));
    }

    /**
     * Admin broadcasts emergency to all hikers at a destination.
     * POST /admin/emergency/broadcast
     */
    public function broadcast(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'severity' => 'required|in:low,medium,high,critical',
            'id_destinasi' => 'required|exists:destinasis,id',
        ]);

        $user = $request->user();

        // Verify admin has access to this destinasi
        $hasAccess = $user->destinasis()->where('destinasis.id', $validated['id_destinasi'])->exists();
        if (!$hasAccess) {
            return back()->with('error', 'Anda tidak memiliki akses ke destinasi ini');
        }

        $emergency = GkEmergencyMessage::create([
            'id_user' => $user->id,
            'id_destinasi' => $validated['id_destinasi'],
            'type' => 'admin_broadcast',
            'title' => $validated['title'],
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'status' => 'active',
        ]);

        broadcast(new EmergencyBroadcast(
            destinasiId: (int) $validated['id_destinasi'],
            emergencyId: $emergency->id,
            title: $emergency->title,
            description: $emergency->description,
            severity: $emergency->severity,
            adminName: $user->biodata?->first_name ?? 'Admin',
        ));

        return back()->with('success', 'Pesan darurat berhasil dibroadcast');
    }

    /**
     * Acknowledge an emergency.
     * PUT /admin/emergency/{id}/acknowledge
     */
    public function acknowledge(Request $request, $id)
    {
        $emergency = GkEmergencyMessage::findOrFail($id);
        $user = $request->user();

        if ($emergency->status !== 'active') {
            return back()->with('error', 'Emergency sudah di-acknowledge');
        }

        $emergency->update([
            'status' => 'acknowledged',
            'acknowledged_by' => $user->id,
        ]);

        return back()->with('success', 'Emergency berhasil di-acknowledge');
    }

    /**
     * Resolve an emergency.
     * PUT /admin/emergency/{id}/resolve
     */
    public function resolve(Request $request, $id)
    {
        $emergency = GkEmergencyMessage::findOrFail($id);
        $user = $request->user();

        $emergency->update([
            'status' => 'resolved',
            'resolved_by' => $user->id,
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Emergency berhasil di-resolve');
    }
}
