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

    public $offeredPrice;
    public $auction_id;
    public $TheBuyer;
    public $message;
    public function __construct(Bid $bid,$name,$message)
    {
       $this->offeredPrice=$bid->offeredPrice;
       $this->auction_id=$bid->auction_id;
       $this->TheBuyer=$name;
       $this->message=$message;
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
            'message' =>$this->message,
            'offeredPrice' =>$this->offeredPrice,
            'auction_id' =>$this->auction_id,
            'TheBuyer' =>$this->TheBuyer,
        ];
    }

}
