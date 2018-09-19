<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('test', function (Request $request) {
    if ($request->isMethod('POST')) {
        $name = $request->input('name');
    }
    return 'hello ' . ($name ?? 'world');
});

Route::group(['namespace' => 'home', 'prefix' => 'home'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});