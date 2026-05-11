<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Models\GkSos;
use App\Models\GkSosChat;
use App\Events\SOSMessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SOSChatController extends Controller
{
    /**
     * Send a chat message in an SOS session.
     * POST /api/sos/chat/{sos_id}/send
     */
    public function send(Request $request, $sosId)
    {
        $validated = $request->validate([
            'type' => 'required|in:text,image',
            'content' => 'required_if:type,text|nullable|string|max:2000',
            'image' => 'required_if:type,image|nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $user = $request->user();
        $sos = GkSos::findOrFail($sosId);

        // Verify access: must be the hiker who triggered OR admin of the destinasi
        if (!$this->canAccessSos($user, $sos)) {
            return ApiResponse::error('Tidak memiliki akses ke SOS ini', null, 403);
        }

        // Determine sender type
        $senderType = $this->getSenderType($user, $sos);

        // Handle image upload
        $content = $validated['content'] ?? '';
        if ($validated['type'] === 'image' && $request->hasFile('image')) {
            $path = $request->file('image')->store('sos/' . $sosId, 'public');
            $content = $path;
        }

        $message = GkSosChat::create([
            'id_sos' => $sos->id,
            'sender_id' => $user->id,
            'sender_type' => $senderType,
            'type' => $validated['type'],
            'content' => $content,
        ]);

        $senderName = $user->biodata?->first_name ?? ($senderType === 'admin' ? 'Admin' : 'Pendaki');

        broadcast(new SOSMessageSent(
            sosId: $sos->id,
            messageId: $message->id,
            senderType: $senderType,
            senderName: $senderName,
            type: $validated['type'],
            content: $content,
        ));

        return ApiResponse::success([
            'id' => $message->id,
            'sender_type' => $senderType,
            'type' => $message->type,
            'content' => $message->content,
            'created_at' => $message->created_at->toISOString(),
        ], 'Pesan terkirim', 201);
    }

    /**
     * Get chat messages for an SOS session (paginated).
     * GET /api/sos/chat/{sos_id}/messages
     */
    public function messages(Request $request, $sosId)
    {
        $user = $request->user();
        $sos = GkSos::findOrFail($sosId);

        if (!$this->canAccessSos($user, $sos)) {
            return ApiResponse::error('Tidak memiliki akses ke SOS ini', null, 403);
        }

        $messages = GkSosChat::where('id_sos', $sosId)
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        // Mark unread messages as read for this user
        GkSosChat::where('id_sos', $sosId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $data = $messages->getCollection()->map(fn($m) => [
            'id' => $m->id,
            'sender_type' => $m->sender_type,
            'sender_name' => $m->sender?->biodata?->first_name ?? ($m->sender_type === 'admin' ? 'Admin' : 'Pendaki'),
            'type' => $m->type,
            'content' => $m->type === 'image' ? Storage::url($m->content) : $m->content,
            'is_read' => $m->is_read,
            'created_at' => $m->created_at->toISOString(),
        ]);

        return ApiResponse::success([
            'messages' => $data,
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    private function canAccessSos($user, GkSos $sos): bool
    {
        // Admin with access to the destinasi
        if (in_array($user->role, ['admin', 'superadmin'])) {
            return $user->destinasis()->where('destinasis.id', $sos->id_destinasi)->exists();
        }
        // Hiker who owns the SOS
        return $sos->pendaki && $sos->pendaki->booking
            && $sos->pendaki->booking->id_user === $user->id;
    }

    private function getSenderType($user, GkSos $sos): string
    {
        if (in_array($user->role, ['admin', 'superadmin'])) {
            return 'admin';
        }
        return 'hiker';
    }
}
