<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Doctors implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message;
    public $id;
    public function __construct($message,$id)
    {
        $this->message = $message;
        $this->id = $id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     *
     */
    public function broadcastOn()
    {
        return new Channel('Doctors-'.$this->id);
    }
    public function broadcastAs()
    {
        return 'Doctors';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message
        ];
    }
}
