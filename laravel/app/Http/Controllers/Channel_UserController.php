<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\ChannelUser;
use App\Models\Channel;
use Illuminate\Support\Facades\DB;

class Channel_UserController extends Controller
{
    public function UserChannelExists(Request $request, int $receiverId)
    {
        error_log("user_channel, user ids: " . $request->user()->id . " " . $receiverId);

        //query is stored without being run
        $senderChannels = ChannelUser::where('user_id', $request->user()->id)->select('channel_id');

        /*I need to see if both users share a private channel before creating a new one,
        I union all sender's private channels with receiver's selecting only channel_id,
        if there are duplicates from the union all like {channel_id: 1}, {channel_id: 1}, it means they share a private channel already
        as channel_users has UNIQUE(channel_id, user_id)
        this assumes private channels are not group chats
        There could be a better way with eloquent
        */
        //groupby with union doesn't get query grouping right, so I use fromSub to groupby the alias.
        $receiverChannelsUnion = DB::query()->fromSub(DB::table('channel_users')->where('user_id', $receiverId)
            ->select('channel_id')->unionAll($senderChannels), 'unionized');

        $channelExists = $receiverChannelsUnion->groupByRaw('channel_id having count(*) >1')->exists();

        return $channelExists;
    }

    public function createChannelUser(Request $request): Response
    {

        //firstOrFail() throws exception if not found and a 404 res is automatically sent
        $receiverId = User::where('name', $request->input("receiverName"))->firstOrFail();
        if (!$this->UserChannelExists($request, $receiverId->id)) {

            $channel = Channel::create();
            error_log("before two channel creations");
            $senderChannelUser = ChannelUser::create([
                'user_id' => $request->user()->id,
                'channel_id' => $channel->id,
            ]);
            $receiverChannelUser = ChannelUser::create([
                'user_id' => $receiverId->id,
                'channel_id' => $channel->id,
            ]);

            return response($channel->id);
        }
        return response('Channel already exists', 400);
    }
    public function getUserChats(Request $request): Response
    {
        //without 'with', channels are retrieved separately (lazy loading). With 'with', they're retrieved in one query 
        $user = User::with('channels')->find($request->user()->id);
        $result = [];
        //eloquent allows retrieving a user's channels as collection without joins
        foreach ($user->channels as $channel) {
            //find the other user of the channel, by same method above.
            $receiver=$channel->users->except($request->user()->id)->select('id', 'name');
            error_log("receiver in getUserChats: ".$receiver);
            array_push($result, (object)['channel_id'=>$channel->id, 'receiver'=>$receiver[0]]);
        }
        return response($result);
    }
}
