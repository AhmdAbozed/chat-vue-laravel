<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Throwable;

class UserController extends Controller
{
    public function signup(Request $request): Response
    {

        error_log($request->input("Username"));
        if (User::where('email', $request->input("Email"))->exists()) {

            return response(json_encode("Email already in-use."), 403);
        }

        if (User::where('name', $request->input("Username"))->exists()) {

            return response(json_encode("Username already exists."), 403);
        }
        //inserts row with specified column values
        $User = User::create([
            'name' => $request->input("Username"),
            'password' => $request->input("Password"),
            'email' => $request->input("Email")
        ]);
        return response($User);
    }
    public function login(Request $request): Response
    {

        error_log($request->input("Username"));

        //Auth::attempt creates the session needed for auth if successful 
        if (Auth::attempt(['name' => $request->input("Username"), 'password' => $request->input("Password")])) {
            error_log($request->user());
            return response(200, 200);
        } else {
            return response(json_encode("Invalid Username or Password."), 403);
        }
    }
}
