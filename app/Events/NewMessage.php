<?php

namespace App\Events;

use App\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('groups.' . $this->conversation->group->id);
    }

    public function broadcastWith()
    {
        return [
            'type' => $this->conversation->type ?? '',
            'message' => $this->conversation->message,
            'created_at' => date_format($this->conversation->created_at,"Y-m-d H:i"),
            'user' => [
                'id' => $this->conversation->user->id,
                'name' => $this->conversation->user->name,
            ]
        ];
    }
}
