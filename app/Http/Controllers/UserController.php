<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\SignupRequest;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Request;

class UserController extends Controller
{
    public function signup(SignupRequest $request, User $user): Response
    {
        return response($user->signUp($request->input("username"), $request->input("password"), $request->input("email")));
    }
    public function login(LoginRequest $request, User $user)
    {
        return response($user->login($request->input("username"), $request->input("password")));
    }

    public function createDemoAccount(LoginRequest $request, UserService $userService, User $user)
    {
        $user = $userService->createDemoAccount($request->input("username"), $request->input("password"), $request->input("email"));
        return response($user);
    }
    public function signout(HttpRequest $request)
    {
        Auth::logout(); // Log the user out
        $cookies = $request->cookies->all();
        foreach ($cookies as $cookieName => $cookieValue) {
        Cookie::queue(Cookie::forget($cookieName));
        } // logout sometimes fails without this
        return response(null, 200);
    }
}
