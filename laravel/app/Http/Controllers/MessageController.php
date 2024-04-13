<?php

namespace App\Http\Controllers;

use App\Http\Requests\Messages\MessagePostRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\MessageService;

class MessageController extends Controller
{
    public function postMessage(MessagePostRequest $request): Response
    {
        $message = Message::query()->create([
            'user_id' => $request->user()->id,
            'channel_id' => $request->input('channel_id'),
            'content' => $request->input('content')
        ]);
        return response($message);
    }

    public function getMessages(Request $request, MessageService $MessageService, $channel_id): Response
    {
        $messages = $MessageService->getMessagesWithNames($request->user()->id, $channel_id);
        return response($messages);
    }
}
