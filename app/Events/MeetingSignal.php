<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingSignal implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $roomId,
        public readonly string $fromUserId,
        public readonly string $type,      // offer | answer | ice-candidate | joined | left | chat
        public readonly array  $payload,   // SDP / ICE / text payload
        public readonly ?string $toUserId = null,  // null = broadcast to all in room
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('meeting.' . $this->roomId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'signal';
    }

    public function broadcastWith(): array
    {
        return [
            'type'    => $this->type,
            'from'    => $this->fromUserId,
            'to'      => $this->toUserId,
            'payload' => $this->payload,
        ];
    }
}
