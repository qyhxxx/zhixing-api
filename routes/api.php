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

Route::group(['namespace' => 'Home', 'prefix' => 'home'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'AuthController@login');
        Route::post('setUser', 'AuthController@setUser');
        Route::get('logout', 'AuthController@logout');
        Route::get('me', 'AuthController@me');
    });
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'useAdminGuard'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'AuthController@login');
        Route::get('logout', 'AuthController@logout');
        Route::get('me', 'AuthController@me');
        Route::post('resetPassword', 'AuthController@resetPassword');
    });
    Route::group(['prefix' => 'manage'], function () {
        Route::get('add', 'ManageController@add');
        Route::get('delete/{id}', 'ManageController@delete');
        Route::get('getList', 'ManageController@getList');
    });
    Route::group(['prefix' => 'product'], function () {
        Route::post('create', 'ProductController@create');
        Route::post('edit', 'ProductController@edit');
        Route::post('uploadImg', 'ProductController@uploadImg');
        Route::get('delete/{id}', 'ProductController@delete');
        Route::get('getList', 'ProductController@getList');
    });
});