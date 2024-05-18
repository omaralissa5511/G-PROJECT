<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Users implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user ;
    public $message;
    public function __construct($message,$user)
    {

        $this->message = $message;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new Channel('reserve');
    }

    public function broadcastAs()
    {
        return 'reserve-'.$this->user;
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'user' => $this->user
        ];
    }

}
