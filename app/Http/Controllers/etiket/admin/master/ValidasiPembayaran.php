<?php

namespace App\Http\Controllers\etiket\admin\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\pembayaran;
use Carbon\Carbon;

class ValidasiPembayaran extends Controller
{
    //
    public function index(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'start_date' => 'nullable|string',
            'end_date' => 'nullable|string',
            'status' => 'nullable|string',
        ]);


        // Retrieve and parse request parameters
        $start_date = $request->start_date ? Carbon::parse($request->start_date) : "all";
        $end_date = $request->end_date ? Carbon::parse($request->end_date) : "all";
        $status = $request->status ?? 'all';

        // Check if request parameters are set
        if (!$request->has('start_date') && !$request->has('end_date') && !$request->has('status')) {
            $start_date = "all";
            $end_date = "all";
            $status = "all";
        }

        // Start query
        $query = pembayaran::query();

        // Apply date filters if both start_date and end_date are provided
        if ($start_date != "all" && $end_date != "all") {
            $query->whereBetween('created_at', [$start_date, $end_date]);
        }

        // Apply status filter if not 'all'
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Order by creation date
        $pembayaran = $query->orderBy('created_at', 'desc')->get();
        // dd($pengajuan);

        if ($start_date == "all" && $end_date == "all") {
            $start_date = Carbon::parse(now())->format('Y-m-d 00:00:00');
            $end_date = Carbon::parse(now())->format('Y-m-d 23:59');
        }

        // Pass parameters to the view for form population
        return view('etiket.admin.master.validasi.daftar', compact('pembayaran', 'start_date', 'end_date', 'status'));
    }

    public function updateAction(Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'keterangan' => 'string|nullable|max:255',
            'pengajuanId' => 'required|integer',
        ]);

        $pembayaran = pembayaran::findOrFail($request->pengajuanId);
        $pembayaran->status = $request->status;
        $pembayaran->keterangan = $request->keterangan ?? '';

        // dd($request->all());
        $pembayaran->save();

        return redirect()->route('admin.master.validasi.daftar')->with('success', 'Pembayaran berhasil diperbarui');
    }
}
