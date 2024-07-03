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
use App\Models\Message;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewMsgSent implements ShouldBroadcastNow
{
    use Dispatchable;
    public Message $message;
    public function __construct( $message)
    {
        $this->message = $message;
    }
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('chat.'.$this->message->channel_id);
    }
    //on pusher it's ".NewMsgSent" with boradcastAs, otherwise App/Events/NewMsgSent 
    public function broadcastAs(): string|null{
        return "NewMsgSent";
    }
    public function broadcastWith(): array
    {
        return ['channel_id' => $this->message->channel_id, 'time'=>date("Y-m-d H:i:s", time()), 'sup'=>'whatsgoig on'];
    }
}
