<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Message;

class MessageService
{
    private $message;
    private $channel;
    public function __construct(Message $message, Channel $channel)
    {
        $this->message = $message;
        $this->channel = $channel;
    }

    public function getMessagesWithNames(int $user_id, $channel_id)
    {
        $channel = $this->channel->query()->find($channel_id);

        //Does user belong to the channel?
        if ($channel->users->find($user_id)->exists()) {
            $channel->users()->updateExistingPivot($user_id, [
                'unread_count' => 0,
            ]);
            $messages = $this->message->query()->where('channel_id', $channel_id)->select('id', 'user_id', 'channel_id', 'content', 'updated_at')->get();
            $messagesWithNames = [];
            foreach ($messages as $message) {
                //$message has all user columns after $message->user, so I replace user object with just the name
                $userName = $message->user->name;
                array_push($messagesWithNames, (object)array_merge((array)json_decode($message), ['user' => $userName]));
            }
            return (array_reverse($messagesWithNames));
        }
    }
}
