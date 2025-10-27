<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // <-- change
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow // <-- change
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('presence-group.' . $this->message->group_id);
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $u = $this->message->user;
        return [
            'id'         => $this->message->id,
            'group_id'   => $this->message->group_id,
            'content'    => $this->message->content,
            'created_at' => $this->message->created_at->toIso8601String(),
            'user'       => [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => method_exists($u, 'getProfilePhotoUrlAttribute') ? $u->profile_photo_url : null,
            ],
        ];
    }
}