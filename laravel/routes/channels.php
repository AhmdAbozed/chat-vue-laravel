<?php

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
    error_log("INSIDE CHANNEL, CHATID: ".$chatId."userID: ".$user->id);
    error_log($chatId);
    return $chatId === 101;
});
