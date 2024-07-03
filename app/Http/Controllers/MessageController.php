<?php

namespace App\Http\Controllers;

use App\Http\Requests\Messages\MessagePostRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\MessageService;

class MessageController extends Controller
{
    public function postMessage( MessageService $MessageService, MessagePostRequest $request): Response
    {
        $message = $MessageService->postMessage($request->user()->id, $request->input('channel_id'), $request->input('message'), $request->input('file'));
        return response($message);
    }

    public function getMessages(Request $request, MessageService $MessageService, $channel_id): Response
    {
        $messages = $MessageService->getMessagesWithNames($request->user()->id, $channel_id, $request->query('withFileToken'),  $request->input('resetUnread'));
        return response($messages);
    }
}
