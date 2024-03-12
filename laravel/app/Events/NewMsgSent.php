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
    public $chat;
    public function __construct()
    {
        $this->chat=(object) ['id' => 101];
    }
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('chat.'.$this->chat->id);
    }
    //on pusher it's ".NewMsgSent" with boradcastAs, otherwise App/Events/NewMsgSent 
    public function broadcastAs(): string|null{
        return "NewMsgSent";
    }
    public function broadcastWith(): array
    {
        return ['id' => $this->chat->id];
    }
}
