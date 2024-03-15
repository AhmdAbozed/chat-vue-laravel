<?php

namespace App\Http\Controllers;

use App\Events\NewMsgSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MessageController extends Controller
{
    public function postMessage(Request $request): Response{
        $message = Message::create([
            'user_id'=>$request->user()->id,
            'channel_id'=>$request->input('channel_id'),
            'content'=>$request->input('content')
        ]);
        //dispatch arguments are for event construct
        NewMsgSent::dispatch($request->input('channel_id'), $request->input('content'));
        return response($message);
    }
    
    public function getMessages(Request $request, $channel_id): Response{
        $messages = Message::where('channel_id', $channel_id)->get();
        $result = [];
        foreach($messages as $message){
            $user = $message->user->name;
            array_push($result, (object)['message'=>$message, 'name'=>$user]);
        }
        //dispatch arguments are for construct
        return response(array_reverse($result));
    }
}
