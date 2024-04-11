<?php

namespace App\Http\Controllers;

use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use App\Services\JoinRequestService;

class JoinRequestController extends Controller
{
    public function createJoinRequest(Request $request, JoinRequestService $requestService): Response
    {
        return response($requestService->createJoinRequest($request->user()->id, $request->input('channelName')));
    }
    public function resolveJoinRequest(Request $request, JoinRequestService $requestService, int $channel_id, int $request_id): Response
    {
        return response($requestService->resolveJoinRequest($request->user()->id, $request_id, $request->input('accepted')));
    }
}
