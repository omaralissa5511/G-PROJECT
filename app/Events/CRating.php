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

class CRating implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rating ;
    public function __construct($rating)
    {

        $this->rating = $rating;
    }

    public function broadcastOn()
    {
        return new Channel('CRating');
    }

    public function broadcastAs()
    {
        return 'CRating';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->rating
        ];
    }

}
