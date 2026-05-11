<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TrackingUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $destinasiId,
        public string $pendakiId,
        public float $latitude,
        public float $longitude,
        public ?float $altitude,
        public string $hikerName,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tracking.' . $this->destinasiId)];
    }

    public function broadcastAs(): string
    {
        return 'tracking.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'pendaki_id' => $this->pendakiId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'altitude' => $this->altitude,
            'hiker_name' => $this->hikerName,
            'timestamp' => now()->toISOString(),
        ];
    }
}
