<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Clas
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct( $message)
    {

        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('class');
    }

    public function broadcastAs()
    {
        return 'class';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message
        ];
    }
}
