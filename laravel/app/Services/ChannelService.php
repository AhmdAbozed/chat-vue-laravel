<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\ChannelUser;
use App\Models\JoinRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ChannelService
{
    private $user;
    private $channelUser;
    private $channel;
    private $joinRequest;
    public function __construct(User $user, Channel $channel, ChannelUser $channelUser, JoinRequest $joinRequest)
    {
        $this->user = $user;
        $this->channel = $channel;
        $this->joinRequest = $joinRequest;
        $this->channelUser = $channelUser;
    }

    private function channelExists(int $user_id, int $receiverId): Collection
    {
        //Do they already share a private channel?
        $senderChannels = $this->user->query()->find($user_id)->channels->filter(function($channel){
            return $channel->type !== 'group';
            //values() reverts array indexing from filter
        })->values();
        $receiverChannels = $this->user->query()->find($receiverId)->channels->filter(function($channel){
            return $channel->type !== 'group';
        })->values();
        //intersect compares ids
        return $senderChannels->intersect($receiverChannels);
    }

    public function createPrivateChannel(int $user_id, string $receiverName): Channel
    {
        // a 404 res is automatically sent on fail
        $receiverUser = User::query()->where('name', $receiverName)->firstOrFail();
        $existingChannel = $this->channelExists($user_id, $receiverUser->id);
        if ($existingChannel->isEmpty()) {
            $channel = $this->channel->query()->create(['type' => 'private']);
            $senderChannelUser = $channel->users()->attach($user_id);
            $receiverChannelUser = $channel->users()->attach($receiverUser->id);

            error_log("channel Created: " . $channel->id);
            return $channel;
        } else {
            error_log("already exists: " . $existingChannel[0]->id);
            return $existingChannel[0];
        }
    }

    public function createGroupChannel(User $user, string $channelName): Channel
    {
        if(!$user->upgraded){
            if(count($user->owned_channels) > 4){
                abort(response('Free channels limit exceeded (5), upgrade to create more.', 403));    
            }
        }
        $existingChannel = $this->channel->query()->where('name', $channelName)->first();
        error_log('existing channel: ' . json_encode($existingChannel));
        if (!$existingChannel) {
            $channel = $this->channel->query()->create(['name' => $channelName, 'type' => 'group']);
            $channel->users()->attach($user->id);
            $channel->owner()->associate($user);
            $channel->save();
            error_log("group channel Created: " . $channel->id);
            return $channel;
        } else {
            abort(response('Channel already exists', 401));
        }
    }

    public function getUserChannels(int $user_id): array
    {
        //without 'with', channels are retrieved separately (lazy loading). With 'with', they're retrieved in one query 
        $user = $this->user::with('channels')->findOrFail($user_id);
        $result = [];

        foreach ($user->channels as $channel) {
            //find the other user of the channel
            if ($channel->type == 'group') {
                array_push($result, (object)['id' => $channel->id, 'name' => $channel->name, 'unreadCount' => $channel->pivot->unread_count, 'type' => 'group']);
            } else if ($channel->type == 'private') {
                $receiver = $channel->users->except($user_id)->select('id', 'name');
                error_log("channel in getUserChats: " . $channel);
                array_push($result, (object)['id' => $channel->id, 'name' => $receiver[0]['name'], 'unreadCount' => $channel->pivot->unread_count, 'type' => 'private']);
            }
        }
        return $result;
    }


    public function getGroupMembers(int $channel_id): array
    {
        //without 'with', channels are retrieved separately (lazy loading). With 'with', they're retrieved in one query 
        $channel = $this->channel::with('users')->findOrFail($channel_id);
        $result = [];

        foreach ($channel->users as $user) {
            array_push($result, (object)['id' => $user->id, 'name' => $user->name]);
        }
        return $result;
    }
}
