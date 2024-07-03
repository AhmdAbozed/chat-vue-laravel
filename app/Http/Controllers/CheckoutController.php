<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Illuminate\Support\Facades\Request as FacadesRequest;

class CheckoutController extends Controller
{
    public function HandleSubsriptionWebhook(CheckoutService $checkoutService, FacadesRequest $request){
        $response = $checkoutService->handleSubscriptionEvent($request);
        return response($response);
    }
}
