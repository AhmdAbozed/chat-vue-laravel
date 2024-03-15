<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;

class NewMsgSent implements ShouldBroadcast
{
    use Dispatchable;
    public $channel_id;
    public $content;
    public function __construct($id, $message)
    {
        $this->channel_id= $id;
        
        $this->content= $message;
    }
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('chat.'.$this->channel_id);
    }
    //on pusher it's ".NewMsgSent" with boradcastAs, otherwise App/Events/NewMsgSent 
    public function broadcastAs(): string|null{
        return "NewMsgSent";
    }
    public function broadcastWith(): array
    {
        return ['channel_id' => $this->channel_id, 'content'=>$this->content];
    }
}
