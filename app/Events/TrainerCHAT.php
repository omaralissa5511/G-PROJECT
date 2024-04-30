<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrainerCHAT implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }


    public function broadcastOn(): Channel
    {
//        return [new PrivateChannel('channel-name'),
//           return 'CHAT'];

        return new Channel('trainer_' . $this->message->trainer_id);
    }
    public function broadcastAs()
    {
        return 'my-event';
    }
    public function broadcastWith()
    {
        return [
            'user_id' => $this->message->user_id,
            'trainer_id' => $this->message->trainer_id,
            'content' => $this->message->content,
            'image' => $this->message->image
        ];
    }
}
