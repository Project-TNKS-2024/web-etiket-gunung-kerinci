<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class EmergencyBroadcast implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $destinasiId,
        public int $emergencyId,
        public string $title,
        public string $description,
        public string $severity,
        public string $adminName,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('emergency.' . $this->destinasiId)];
    }

    public function broadcastAs(): string
    {
        return 'emergency.broadcast';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->emergencyId,
            'title' => $this->title,
            'description' => $this->description,
            'severity' => $this->severity,
            'admin_name' => $this->adminName,
            'timestamp' => now()->toISOString(),
        ];
    }
}
