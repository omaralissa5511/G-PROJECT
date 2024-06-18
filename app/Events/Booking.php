<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Booking implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {

        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('booking');
    }

    public function broadcastAs()
    {
        return 'booking';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message
        ];
    }
}
