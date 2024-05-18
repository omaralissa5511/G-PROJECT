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

class Bids implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bid ;
    public function __construct($bid)
    {

        $this->bid = $bid;
    }

    public function broadcastOn()
    {
        return new Channel('bid');
    }

    public function broadcastAs()
    {
        return 'bid';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->bid
        ];
    }

}
