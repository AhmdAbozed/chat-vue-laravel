<?php

use App\Models\Channel;
use App\Models\ChannelUser;
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//Callback first argument is User authenticated on this request
//{chatId} is a wildcard parameter. Passed to callback as second argument
Broadcast::channel('chat.{chatId}', function (User $user, int $chatId) {
    error_log("INSIDE CHANNEL, CHATID: ".$chatId." userID: ".$user->id);
    return ChannelUser::where('user_id', $user->id)->where('channel_id', $chatId)->get()->isNotEmpty();
});
