<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public Message $message)
    {
        $this->message->load('sender');
    }

    public function broadcastOn()
    {
        return new PrivateChannel(
            'conversation.' . $this->message->conversation_id
        );
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}
