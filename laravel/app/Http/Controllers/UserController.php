<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\SignupRequest;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use App\Models\User;
class UserController extends Controller
{
    public function signup(SignupRequest $request, User $user): Response
    {
        return response($user->signUp($request->input("Username"), $request->input("Password"), $request->input("Email")));
    }
    public function login(LoginRequest $request, User $user)
    {
            return response($user->login($request->input("Username"), $request->input("Password")));
        
    }
}
