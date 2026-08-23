<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class SOSTriggered implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $destinasiId,
        public int $sosId,
        public string $severity,
        public float $latitude,
        public float $longitude,
        public string $hikerName,
        public ?string $message,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('sos.' . $this->destinasiId)];
    }

    public function broadcastAs(): string
    {
        return 'sos.triggered';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->sosId,
            'severity' => $this->severity,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'hiker_name' => $this->hikerName,
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
        ];
    }
}
