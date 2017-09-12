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

//LOGIN API

Route::group(['namespace' => 'API'], function(){
	Route::post('/check/device',	['uses' => 'LoginController@post_login_device']);
	Route::post('/check/user',		['uses' => 'LoginController@post_login_with_username']);
	Route::post('/permohonan',		['uses' => 'PermohonanController@store', 'middleware' => 'device']);
});
