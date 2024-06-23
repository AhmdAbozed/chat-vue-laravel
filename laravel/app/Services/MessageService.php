<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class MessageService
{
    private $message;
    private $channel;
    private $fileHandler;
    public function __construct(Message $message, Channel $channel, BackBlazeService $fileHandler)
    {
        $this->message = $message;
        $this->channel = $channel;
        $this->fileHandler = $fileHandler;
    }
    public function getMessagesWithNames(int $user_id, $channel_id, bool $withFileToken, bool $reset_unread)
    {
        $channel = $this->channel->query()->find($channel_id);

        //Does user belong to the channel?
        if ($channel->users->find($user_id)->exists()) {

            //reseting unread on GET
            if($reset_unread){
                $channel->users()->updateExistingPivot($user_id, [
                    'unread_count' => 0,
                ]);
            }
            $messages = $this->message->query()->where('channel_id', $channel_id)->select('id', 'user_id', 'channel_id', 'content', 'updated_at', 'file_name', 'file_size')->get();
            $messagesWithNames = [];
            foreach ($messages as $message) {
                //$message has all user columns after $message->user, so I replace user object with just the name
                $userName = $message->user->name;
                array_push($messagesWithNames, (object)array_merge((array)json_decode($message), ['user' => $userName]));
            }

            if ($withFileToken) {
                $fileToken = $this->fileHandler->getDownloadAuth($channel_id);   
                return ['messages' => array_reverse($messagesWithNames), 'fileToken' => $fileToken->authorizationToken, 'fileUrl' => $fileToken->apiUrl];
            } else {
                return ['messages' => array_reverse($messagesWithNames)];
            }
        }
    }

    public function postMessage(int $user_id, int $channel_id, string $content, $file)
    {
        $message = Message::query()->create([
            'user_id' => $user_id,
            'channel_id' => $channel_id,
            'content' => $content,
            'file_name' => $file ? $file->getClientOriginalName() : null,
            'file_size'=> $file ? File::size($file) : null,
        ]);
        if ($file) {
            $this->fileHandler->uploadFile($file, $channel_id);
        }
        return $message;
    }
}
