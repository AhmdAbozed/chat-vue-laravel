<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use App\Models\User;
class UserController extends Controller
{
    public function signup(Request $request, User $user): Response
    {
        return response($user->signUp($request->input("Username"), $request->input("Password"), $request->input("Email")));
    }
    public function login(Request $request, User $user)
    {
            return response($user->login($request->input("Username"), $request->input("Password")));
        
    }
}
