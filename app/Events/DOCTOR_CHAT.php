<?php

namespace App\Events;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DOCTOR_CHAT implements ShouldBroadcast
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userID;
    public $doctor_id;

    public function __construct($userID,$doctor_id,$message)
    {
        $this->message = $message;
        $this->userID = $userID;
        $this->doctor_id = $doctor_id;
    }


    public function broadcastOn(): Channel
    {
        return new PrivateChannel
        ("chat-Doctor-".$this->message->user_id.'-'.$this->message->doctor_id);

    }
    public function broadcastAs()
    {
        return 'DOCTOR';
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->message->user_id,
            'doctor_id' => $this->message->doctor_id,
            'content' => $this->message->content,
            'user' => $this->message->user,
            'doctor' => $this->message->doctor,
            'role' => $this->message->role,
            'image' => $this->message->image,
            'time' => $this->message->time,
        ];
    }
}
