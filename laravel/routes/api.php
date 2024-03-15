<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Events\NewMsgSent;
use App\Http\Controllers\Channel_UserController;
use App\Http\Controllers\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/testauth', function (Request $request) {
    error_log($request->user()->id);
    return $request->user()->id;
});
Route::middleware('auth:sanctum')->post('/chats', [Channel_UserController::class, 'createChannelUser']);
Route::middleware('auth:sanctum')->get('/chats', [Channel_UserController::class, 'getUserChats']);
Route::middleware('auth:sanctum')->post('/chats/{id}/messages', [MessageController::class, 'postMessage']);
Route::middleware('auth:sanctum')->get('/chats/{id}/messages', [MessageController::class, 'getMessages']);

Route::post('/users/signup', [UserController::class, 'signup']);
Route::post('/users/login', [UserController::class, 'login']);
