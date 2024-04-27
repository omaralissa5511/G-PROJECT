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

class NewUSERAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $user_name;
    public $message;
    public function __construct($user_name,$message)
    {

        $this->user_name = $user_name;
        $this->message = $message;

    }


    public function broadcastOn(): array
    {
        return [

          //  new PrivateChannel('channel-name'),
            'CHAT'

        ];
    }
    public function broadcastAs()
    {
        return 'my-event1122';
    }
}
