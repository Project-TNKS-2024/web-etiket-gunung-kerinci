<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class SOSStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $destinasiId,
        public int $sosId,
        public string $oldStatus,
        public string $newStatus,
        public string $adminName,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('sos.' . $this->destinasiId),
            new PrivateChannel('sos-chat.' . $this->sosId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'sos.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->sosId,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'admin_name' => $this->adminName,
            'timestamp' => now()->toISOString(),
        ];
    }
}
