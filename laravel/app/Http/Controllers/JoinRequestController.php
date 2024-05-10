<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinRequests\JoinPostRequest;
use App\Http\Requests\JoinRequests\JoinResolveRequest;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use App\Services\JoinRequestService;

class JoinRequestController extends Controller
{
    public function createJoinRequest(JoinPostRequest $request, JoinRequestService $requestService): Response
    {
        return response($requestService->createJoinRequest($request->user()->id, $request->input('channelName')));
    }
    public function resolveJoinRequest(JoinResolveRequest $request, JoinRequestService $requestService, int $request_id): Response
    {
        return response($requestService->resolveJoinRequest($request->user()->id, $request_id, $request->input('accepted')));
    }
}
