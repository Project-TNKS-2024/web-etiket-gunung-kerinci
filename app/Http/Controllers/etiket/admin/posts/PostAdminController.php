<?php

namespace App\Http\Controllers\etiket\admin\posts;

use App\Http\Controllers\Controller;
use App\Models\GkPost;
use App\Models\gk_gates;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostAdminController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $destinasiIds = $user->destinasis()->pluck('destinasis.id');
        $gates = gk_gates::whereIn('id_destinasi', $destinasiIds)->with('destinasi')->get();
        $gateIds = $gates->pluck('id');

        $posts = GkPost::whereIn('id_gate', $gateIds)
            ->with('gate.destinasi')
            ->orderBy('id_gate')
            ->orderBy('urutan')
            ->get();

        return view('etiket.admin.posts.index', compact('posts', 'gates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'required|integer|min:1',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'altitude' => 'nullable|integer',
            'radius_meter' => 'nullable|integer|min:50|max:500',
            'id_gate' => 'required|exists:gk_gates,id',
            'detail' => 'nullable|string|max:1000',
        ]);

        $validated['qr_code_value'] = 'POST-' . strtoupper(Str::random(12));
        $validated['radius_meter'] = $validated['radius_meter'] ?? 150;

        GkPost::create($validated);

        return back()->with('success', 'Post berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $post = GkPost::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'required|integer|min:1',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'altitude' => 'nullable|integer',
            'radius_meter' => 'nullable|integer|min:50|max:500',
            'id_gate' => 'required|exists:gk_gates,id',
            'detail' => 'nullable|string|max:1000',
            'status' => 'nullable|boolean',
        ]);

        $post->update($validated);

        return back()->with('success', 'Post berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $post = GkPost::findOrFail($id);
        $post->delete();

        return back()->with('success', 'Post berhasil dihapus');
    }
}
