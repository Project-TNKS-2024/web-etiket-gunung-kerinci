<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class SOSMessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $sosId,
        public int $messageId,
        public string $senderType,
        public string $senderName,
        public string $type,
        public string $content,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('sos-chat.' . $this->sosId)];
    }

    public function broadcastAs(): string
    {
        return 'sos.message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->messageId,
            'sender_type' => $this->senderType,
            'sender_name' => $this->senderName,
            'type' => $this->type,
            'content' => $this->content,
            'timestamp' => now()->toISOString(),
        ];
    }
}
