<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use App\Services\ChannelService;

class ChannelController extends Controller
{
    public function createChannel(Request $request, ChannelService $channelService): Response
    {
        return response($channelService->createChannel($request), 200);
    }
    public function getUserChannels(Request $request, ChannelService $channelService): Response
    {
        return response($channelService->getUserChannels($request));
    }
}
