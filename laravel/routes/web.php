<?php

use Illuminate\Support\Facades\Route;

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
Route::middleware('auth:sanctum')->get('/', ['as'=>'homePage', 'uses'=>function () {
    return Inertia::render('homePage');
}]);

Route::get('/login',['as'=>'login', 'uses'=>function () {
    return Inertia::render('auth/LogIn');
}]);

Route::get('/signup', function () {
    return Inertia::render('auth/SignUp');
});
