<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/welcome', function () {
    return view('welcome');
});

//'as' names route for reference in controllers
Route::middleware('auth:sanctum')->get('/', ['as'=>'homePage', 'uses'=>function (Request $request) {
    
    return Inertia::render('homePage', ['signedUser'=>$request->user()]);
}]);
Route::middleware('auth:sanctum')->get('/upgrade', ['as'=>'subscriptionPage', 'uses'=>function (Request $request) {
    return Inertia::render('subscriptionPage', ['signedUser'=>$request->user()]);
}]);

Route::get('/login',['as'=>'login', 'uses'=>function () {
    return Inertia::render('auth/LogIn');
}]);

Route::get('/signup', function () {
    return Inertia::render('auth/SignUp');
});
