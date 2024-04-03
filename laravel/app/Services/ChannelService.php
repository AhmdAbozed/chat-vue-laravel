<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\ChannelUser;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ChannelService
{
    private $user;
    private $channelUser;
    private $channel;
    public function __construct(User $user, Channel $channel, ChannelUser $channelUser)
    {
        $this->user = $user;
        $this->channel = $channel;
        $this->channelUser = $channelUser;
    }

    private function ChannelExists(Request $request, int $receiverId): Collection
    {
        //Do they already share a channel?
        $senderChannels = $this->user->query()->find($request->user()->id)->channels;
        $receiverChannels = $this->user->query()->find($receiverId)->channels;
        //intersect compares ids
        return $senderChannels->intersect($receiverChannels);
    }

    public function createChannel(Request $request): Channel
    {

        //firstOrFail() throws exception if not found and a 404 res is automatically sent
        $receiverUser = User::query()->where('name', $request->input("receiverName"))->firstOrFail();
        $existingChannel = $this->ChannelExists($request, $receiverUser->id);
        if ($existingChannel->isEmpty()) {
            $channel = $this->channel->query()->create([]);
            $senderChannelUser = $channel->users()->attach($request->user()->id);
            $receiverChannelUser = $channel->users()->attach($receiverUser->id);

            error_log("channel Created: " . $channel->id);
            return $channel;
        } else {
            error_log("already exists: " . $existingChannel[0]->id);
            return $existingChannel[0];
        }
    }
    public function getUserChannels(Request $request): array
    {
        //without 'with', channels are retrieved separately (lazy loading). With 'with', they're retrieved in one query 
        $user = $this->user::with('channels')->findOrFail($request->user()->id);
        $result = [];

        foreach ($user->channels as $channel) {
            //find the other user of the channel
            $receiver = $channel->users->except($request->user()->id)->select('id', 'name');
            error_log("channel in getUserChats: " . $channel);
            array_push($result, (object)['id' => $channel->id, 'receiver' => $receiver[0], 'unreadCount' => $channel->pivot->unread_count]);
        }
        return $result;
    }
}
