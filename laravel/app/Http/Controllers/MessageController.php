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
        $messages = Message::where('channel_id', $channel_id)->select('id', 'user_id', 'channel_id', 'content', 'updated_at')->get();
        $result = [];
        foreach($messages as $message){
            
            //running $message->user at first runs a query to select user,
            //then appends user object to $message, without select
            //but now $message has all user columns, so I replace user object with just the name

            error_log('message to return: '.$message);
            $userName = $message->user->name;
            array_push($result, (object)array_merge((array)json_decode($message), ['user'=> $userName]));
        
        }
        //dispatch arguments are for construct
        return response(array_reverse($result));
    }
}
