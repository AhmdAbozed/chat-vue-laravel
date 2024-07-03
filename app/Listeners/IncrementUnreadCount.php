<?php

namespace App\Listeners;

use App\Events\NewMsgSent;

use App\Models\ChannelUser;

class IncrementUnreadCount
{
    /**
     * Create the event listener.
     */    public function __construct(
        protected ChannelUser $ChannelUser,
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(NewMsgSent $event): void
    {
        $incrementUnread = $this->ChannelUser::query()->where('channel_id', $event->message->channel_id)->whereNot('user_id', $event->message->user_id)
            ->incrementEach(['unread_count' => 1])->get();
    }
}
