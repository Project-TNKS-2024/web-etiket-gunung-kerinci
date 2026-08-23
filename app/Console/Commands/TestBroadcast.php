<?php

namespace App\Console\Commands;

use App\Events\EmergencyTriggered;
use Illuminate\Console\Command;

class TestBroadcast extends Command
{
    protected $signature = 'test:broadcast {--destinasi=1 : Destinasi ID to broadcast to}';
    protected $description = 'Test WebSocket broadcasting by sending a test emergency event';

    public function handle(): void
    {
        $destinasiId = (int) $this->option('destinasi');

        broadcast(new EmergencyTriggered(
            destinasiId: $destinasiId,
            emergencyId: 999,
            title: 'Test Broadcast',
            description: 'This is a test broadcast to verify WebSocket connectivity.',
            severity: 'low',
            latitude: -1.6974,
            longitude: 101.2642,
            hikerName: 'Test Hiker',
        ));

        $this->info("Test event broadcasted to private-emergency.{$destinasiId}");
        $this->info('Check admin panel or browser console for the event.');
    }
}
