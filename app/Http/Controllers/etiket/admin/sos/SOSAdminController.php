<?php

namespace App\Http\Controllers\etiket\admin\sos;

use App\Http\Controllers\Controller;
use App\Models\GkSos;
use App\Models\GkSosChat;
use App\Models\GkDisasterReport;
use App\Events\SOSStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SOSAdminController extends Controller
{
    /**
     * SOS list page.
     * GET /admin/sos
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $destinasiIds = $user->destinasis()->pluck('destinasis.id');

        $sosList = GkSos::whereIn('id_destinasi', $destinasiIds)
            ->with(['pendaki.biodata', 'destinasi'])
            ->orderByRaw("FIELD(status, 'active', 'acknowledged', 'dispatched', 'resolved', 'false_alarm')")
            ->orderByDesc('created_at')
            ->paginate(20);

        $activeCount = GkSos::whereIn('id_destinasi', $destinasiIds)
            ->whereIn('status', ['active', 'acknowledged', 'dispatched'])->count();

        $disasterReports = GkDisasterReport::whereIn('id_destinasi', $destinasiIds)
            ->where('status', 'pending')
            ->with(['user.biodata', 'destinasi'])
            ->orderByDesc('created_at')
            ->get();

        return view('etiket.admin.sos.index', compact('sosList', 'activeCount', 'disasterReports'));
    }

    /**
     * SOS detail + chat page.
     * GET /admin/sos/{id}
     */
    public function detail(Request $request, $id)
    {
        $sos = GkSos::with(['pendaki.biodata', 'booking', 'destinasi'])->findOrFail($id);

        $messages = GkSosChat::where('id_sos', $id)
            ->with('sender.biodata')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender?->biodata?->first_name ?? ($m->sender_type === 'admin' ? 'Admin' : 'Pendaki'),
                'type' => $m->type,
                'content' => $m->type === 'image' ? Storage::url($m->content) : $m->content,
                'created_at' => $m->created_at->format('H:i'),
                'date' => $m->created_at->format('d/m/Y'),
            ]);

        return view('etiket.admin.sos.detail', compact('sos', 'messages'));
    }

    /**
     * Update SOS status.
     * PUT /admin/sos/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:acknowledged,dispatched,resolved,false_alarm',
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        $sos = GkSos::findOrFail($id);
        $user = $request->user();
        $oldStatus = $sos->status;

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'acknowledged') {
            $updateData['acknowledged_by'] = $user->id;
        }

        if (in_array($validated['status'], ['resolved', 'false_alarm'])) {
            $updateData['resolved_by'] = $user->id;
            $updateData['resolved_at'] = now();
            $updateData['resolution_notes'] = $validated['resolution_notes'] ?? null;
        }

        $sos->update($updateData);

        broadcast(new SOSStatusUpdated(
            destinasiId: $sos->id_destinasi,
            sosId: $sos->id,
            oldStatus: $oldStatus,
            newStatus: $validated['status'],
            adminName: $user->biodata?->first_name ?? 'Admin',
        ));

        return back()->with('success', 'Status SOS diperbarui ke: ' . $validated['status']);
    }

    /**
     * Verify or reject a disaster report.
     * PUT /admin/disaster-report/{id}/verify
     */
    public function verifyDisasterReport(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $report = GkDisasterReport::findOrFail($id);
        $user = $request->user();

        $report->update([
            'status' => $validated['status'],
            'verified_by' => $user->id,
            'verified_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Laporan bencana ' . ($validated['status'] === 'verified' ? 'diverifikasi' : 'ditolak'));
    }
}
