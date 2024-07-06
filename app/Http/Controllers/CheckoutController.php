<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function HandleSubsriptionWebhook(CheckoutService $checkoutService, Request $request){
        $response = $checkoutService->handleSubscriptionEvent($request);
        return response($response);
    }
}
