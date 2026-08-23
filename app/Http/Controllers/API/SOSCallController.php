<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Models\GkDisasterReport;
use App\Models\gk_pendaki;
use App\Models\setting;
use App\Events\DisasterReported;
use Illuminate\Http\Request;

class SOSCallController extends Controller
{
    /**
     * Get rescue team call options with WhatsApp deep links.
     * GET /api/sos/call-options
     */
    public function callOptions(Request $request)
    {
        $user = $request->user();
        $pendaki = $this->getActivePendaki($user);

        $contacts = [];

        // Get rescue phone from settings
        $rescuePhone = setting::where('nama', 'rescue_phone')->first();
        if ($rescuePhone && $rescuePhone->text1) {
            $phone = preg_replace('/[^0-9]/', '', $rescuePhone->text1);
            $name = $rescuePhone->text2 ?? 'Tim SAR';

            $prefilledMsg = urlencode("SOS - Saya butuh bantuan di Gunung Kerinci.");
            if ($pendaki) {
                $prefilledMsg = urlencode(
                    "SOS - {$pendaki->fullName} butuh bantuan. Booking: {$pendaki->booking_id}"
                );
            }

            $contacts[] = [
                'name' => $name,
                'phone' => $rescuePhone->text1,
                'whatsapp_link' => "https://wa.me/{$phone}?text={$prefilledMsg}",
                'tel_link' => "tel:{$rescuePhone->text1}",
            ];
        }

        // Get additional emergency contacts from settings
        $emergencyPhone = setting::where('nama', 'emergency_phone')->first();
        if ($emergencyPhone && $emergencyPhone->text1) {
            $phone = preg_replace('/[^0-9]/', '', $emergencyPhone->text1);
            $contacts[] = [
                'name' => $emergencyPhone->text2 ?? 'Kontak Darurat',
                'phone' => $emergencyPhone->text1,
                'whatsapp_link' => "https://wa.me/{$phone}",
                'tel_link' => "tel:{$emergencyPhone->text1}",
            ];
        }

        return ApiResponse::success($contacts, 'Kontak darurat');
    }

    /**
     * Submit disaster potential report.
     * POST /api/sos/disaster-report
     */
    public function disasterReport(Request $request)
    {
        $validated = $request->validate([
            'potensi_bencana' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:2000',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'lampiran' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $user = $request->user();
        $pendaki = $this->getActivePendaki($user);

        if (!$pendaki) {
            return ApiResponse::error('Tidak ada pendakian aktif', null, 403);
        }

        $destinasiId = $pendaki->booking->destinasi?->id;
        if (!$destinasiId) {
            return ApiResponse::error('Destinasi tidak ditemukan', null, 404);
        }

        // Handle image upload
        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('disaster-reports', 'public');
        }

        $report = GkDisasterReport::create([
            'id_user' => $user->id,
            'id_destinasi' => $destinasiId,
            'id_booking' => $pendaki->booking_id,
            'potensi_bencana' => $validated['potensi_bencana'],
            'deskripsi' => $validated['deskripsi'],
            'lokasi' => $validated['lokasi'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'lampiran' => $lampiranPath,
            'status' => 'pending',
        ]);

        broadcast(new DisasterReported(
            destinasiId: $destinasiId,
            reportId: $report->id,
            potensiBencana: $report->potensi_bencana,
            lokasi: $report->lokasi,
            hikerName: $pendaki->fullName ?? 'Unknown',
        ));

        return ApiResponse::success([
            'id' => $report->id,
            'status' => 'pending',
            'created_at' => $report->created_at->toISOString(),
        ], 'Laporan bencana berhasil dikirim', 201);
    }

    /**
     * Get my disaster reports.
     * GET /api/sos/disaster-reports
     */
    public function myReports(Request $request)
    {
        $user = $request->user();

        $reports = GkDisasterReport::where('id_user', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'potensi_bencana' => $r->potensi_bencana,
                'lokasi' => $r->lokasi,
                'status' => $r->status,
                'created_at' => $r->created_at->toISOString(),
            ]);

        return ApiResponse::success($reports);
    }

    private function getActivePendaki($user): ?gk_pendaki
    {
        return gk_pendaki::whereHas('booking', function ($q) use ($user) {
            $q->where('id_user', $user->id)->where('status_booking', 6);
        })->first();
    }
}
