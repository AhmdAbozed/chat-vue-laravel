<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\ChannelUser;
use App\Models\JoinRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class JoinRequestService
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

    public function getGroupRequests(int $owner_id, int $channel_id): array
    {
        //without 'with', channels are retrieved separately (lazy loading). With 'with', they're retrieved in one query 
        $channel = $this->channel::with('requests')->findOrFail($channel_id);
        $result = [];
        if (!($owner_id == $channel->owner->id)) abort(response()->json('Unauthorized', 403));


        foreach ($channel->requests as $request) {
            if ($request->status == 'pending') {
                $user = $request->user;
                array_push($result, (object)['id' => $request->id, 'user_id' => $user->id, 'channel_id' => $channel_id, 'name' => $user->name]);
            }
        }
        return $result;
    }
    public function createJoinRequest(int $user_id, string $channelName)
    {
        $channel = $this->channel->query()->where('name', $channelName)->firstOrFail();
        if ($channel->type == 'group') {
            return $channel->requests()->create(['user_id' => $user_id, 'status' => 'pending']);
        }
    }

    public function resolveJoinRequest(int $owner_id, int $request_id, bool $accepted)
    {


        $joinRequest = $this->joinRequest->query()->find($request_id);
        $channelToJoin = $joinRequest->channel;

        if ($joinRequest->status == 'pending' && $channelToJoin->owner->id == $owner_id) {
            if (!$channelToJoin->owner->upgraded) {

                error_log('NOT UPGRADED'.count($channelToJoin->users));
                if (count($channelToJoin->users) > 1) {

                    error_log('DENIED');
                    abort(response('Members limit exceeded (10), upgrade to add more.', 403));
                }
            }
            if ($accepted) {
                $newChannelUser = $channelToJoin->users()->attach($joinRequest->user->id);
                $joinRequest->status = 'accepted';
                $joinRequest->save();
                return $joinRequest;
            } else {
                $joinRequest->status = 'rejected';
                $joinRequest->save();
                return $joinRequest;
            }
        }
    }
}
