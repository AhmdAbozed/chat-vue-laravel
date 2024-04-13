<?php

namespace App\Http\Controllers;

use App\Http\Requests\Channels\GroupPostRequest;
use App\Http\Requests\Channels\PrivateChannelPostRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use App\Services\ChannelService;
use App\Services\JoinRequestService;

class ChannelController extends Controller
{   
    
    public function createGroupChannel(GroupPostRequest $request, ChannelService $channelService): Response
    {
        return response($channelService->createGroupChannel($request->user(), $request->input('channelName')));
    }
    
    public function createChannel(PrivateChannelPostRequest $request, ChannelService $channelService): Response
    {
        return response($channelService->createPrivateChannel($request->user()->id, $request->input('receiverName')));
    }
    
    public function getUserChannels(Request $request, ChannelService $channelService): Response
    {
        return response($channelService->getUserChannels($request->user()->id));
    }
    
    public function getGroupMembers(ChannelService $channelService, int $channel_id): Response
    {
        return response($channelService->getGroupMembers($channel_id));
    }

    public function getGroupRequests(Request $request, JoinRequestService $requestService, int $channel_id): Response
    {
        return response($requestService->getGroupRequests($request->user()->id, $channel_id));
    }
}
