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

class CHAT implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userID;
    public $trainer_id;

    public function __construct($userID,$trainer_id,$message)
    {
        $this->message = $message;
        $this->userID = $userID;
        $this->trainer_id = $trainer_id;
    }


    public function broadcastOn(): Channel
    {
        return new PrivateChannel("private.chat".$this->userID.'-'.$this->trainer_id);

    }
    public function broadcastAs()
    {
        return 'BENZO';
    }
    public function broadcastWith()
    {
        return [
            'user_id' => $this->message->user_id,
            'trainer_id' => $this->message->trainer_id,
            'content' => $this->message->content,
            'user' => $this->message->user,
            'trainer' => $this->message->trainer,
            'role' => $this->message->role,
            'image' => $this->message->image
        ];
    }
}
