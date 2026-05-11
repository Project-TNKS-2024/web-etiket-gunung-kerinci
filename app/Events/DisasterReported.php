<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class DisasterReported implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $destinasiId,
        public int $reportId,
        public string $potensiBencana,
        public string $lokasi,
        public string $hikerName,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('sos.' . $this->destinasiId)];
    }

    public function broadcastAs(): string
    {
        return 'disaster.reported';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->reportId,
            'potensi_bencana' => $this->potensiBencana,
            'lokasi' => $this->lokasi,
            'hiker_name' => $this->hikerName,
            'timestamp' => now()->toISOString(),
        ];
    }
}
