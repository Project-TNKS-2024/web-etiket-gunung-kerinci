<?php

namespace App\Http\Controllers\etiket\admin\posts;

use App\Http\Controllers\Controller;
use App\Models\GkPost;
use App\Models\gk_gates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PostAdminController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $destinasiIds = $user->destinasis()->pluck('destinasis.id');
        $gates = gk_gates::whereIn('id_destinasi', $destinasiIds)->with('destinasi')->get();
        $gateIds = $gates->pluck('id');

        $filters = $request->validate([
            'gate_id' => ['nullable', 'integer', Rule::in($gateIds->all())],
            'q' => 'nullable|string|max:100',
        ]);

        $posts = GkPost::whereIn('id_gate', $gateIds)
            ->with('gate.destinasi')
            ->when($filters['gate_id'] ?? null, fn ($query, $gateId) => $query->where('id_gate', $gateId))
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('nama', 'like', '%' . $search . '%'))
            ->orderBy('id_gate')
            ->orderBy('urutan')
            ->get();

        return view('etiket.admin.posts.index', compact('posts', 'gates'));
    }

    public function store(Request $request)
    {
        $this->savePost($request);

        return back()->with('success', 'Post berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $post = GkPost::whereIn('id_gate', $this->managedGateIds($request))->findOrFail($id);

        $this->savePost($request, $post);

        return back()->with('success', 'Post berhasil diperbarui');
    }

    private function validatePost(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120|dimensions:max_width=8192,max_height=8192',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'altitude' => 'nullable|integer|min:0|max:2147483647',
            'water_source_status' => ['required', Rule::in(['Tersedia', 'Tidak tersedia', 'Belum diketahui'])],
            'distance_to_summit_m' => 'nullable|integer|min:0|max:2147483647',
            'camping_status' => ['required', Rule::in(['Boleh', 'Tidak boleh', 'Belum diketahui'])],
            'radius_meter' => 'nullable|integer|min:50|max:500',
            'id_gate' => ['required', 'integer', Rule::exists('gk_gates', 'id')->whereIn('id', $this->managedGateIds($request)->all())],
            'detail' => 'nullable|string|max:1000',
            'status' => 'sometimes|boolean',
        ]);
    }

    private function savePost(Request $request, ?GkPost $post = null): void
    {
        $validated = $this->validatePost($request);
        unset($validated['image']);
        $uploaded = $request->file('image')?->store('post-images', 'public');
        if ($uploaded === false) {
            throw ValidationException::withMessages(['image' => 'Gambar belum berhasil disimpan. Coba unggah lagi.']);
        }
        $oldImage = null;
        try {
            DB::transaction(function () use ($post, $validated, $uploaded, &$oldImage) {
                $post = $post ? GkPost::whereKey($post->id)->lockForUpdate()->firstOrFail() : new GkPost();
                $isNew = ! $post->exists;
                $oldImage = $post->map_image_path;
                $validated['radius_meter'] = $validated['radius_meter'] ?? $post->radius_meter ?? 150;
                $post->fill($validated);
                if ($uploaded) {
                    $post->map_image_path = 'storage/'.$uploaded;
                }
                if ($isNew || $post->isDirty(['altitude', 'water_source_status', 'distance_to_summit_m', 'camping_status', 'map_image_path'])) {
                    $post->info_updated_at = now();
                }
                $post->save();
                if ($isNew) $post->update(['qr_code_value' => 'TNKS_CHECKPOINT:'.$post->id]);
            });
        } catch (\Throwable $error) {
            if ($uploaded) Storage::disk('public')->delete($uploaded);
            throw $error;
        }
        if ($uploaded) $this->deleteUnusedImage($oldImage);
    }

    private function deleteUnusedImage(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/post-images/')
            && ! GkPost::where('map_image_path', $path)->exists()) {
            if (! Storage::disk('public')->delete(substr($path, strlen('storage/')))) {
                Log::warning('Gambar pos lama belum terhapus setelah data diperbarui.', ['path' => $path]);
            }
        }
    }

    private function managedGateIds(Request $request): \Illuminate\Support\Collection
    {
        $destinasiIds = $request->user()->destinasis()->pluck('destinasis.id');

        return gk_gates::whereIn('id_destinasi', $destinasiIds)->pluck('id');
    }

    public function destroy(Request $request, $id)
    {
        $post = GkPost::whereIn('id_gate', $this->managedGateIds($request))->findOrFail($id);
        $image = $post->map_image_path;
        $post->delete();
        $this->deleteUnusedImage($image);

        return back()->with('success', 'Post berhasil dihapus');
    }
}
