<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class EmergencyTriggered implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $destinasiId,
        public int $emergencyId,
        public string $title,
        public string $description,
        public string $severity,
        public ?float $latitude,
        public ?float $longitude,
        public ?string $hikerName,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('emergency.' . $this->destinasiId)];
    }

    public function broadcastAs(): string
    {
        return 'emergency.triggered';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->emergencyId,
            'title' => $this->title,
            'description' => $this->description,
            'severity' => $this->severity,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'hiker_name' => $this->hikerName,
            'timestamp' => now()->toISOString(),
        ];
    }
}
