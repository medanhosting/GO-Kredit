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
	Route::post('/check/device',				['uses' => 'LoginController@post_login_device']);
	Route::post('/check/user',					['uses' => 'LoginController@post_login_with_username']);
	Route::post('/permohonan',					['uses' => 'PermohonanController@store', 'middleware' => 'device']);
	Route::get('/permohonan',					['uses' => 'PermohonanController@index', 'middleware' => 'device']);
	Route::post('/foto/survei/{pengajuan_id}',	['uses' => 'SurveiController@upload_foto', 'middleware' => 'device']);
});

Route::get('/pengaturan', function (Request $request) 
{
	if($request->has('nip_karyawan'))
	{
		return Response::json(['minimum_pengajuan' => 2500000, 'minimum_shgb' => Carbon\Carbon::now()->format('Y'), 'remain_pengajuan' => 1]);
	}

	$phone 			= $request->get('mobile');
	$jlh_pengajuan	= \Thunderlabid\Pengajuan\Models\Pengajuan::status('permohonan')->where('nasabah->telepon', $phone['telepon'])->count();

	return Response::json(['minimum_pengajuan' => 2500000, 'minimum_shgb' => Carbon\Carbon::now()->format('Y'), 'remain_pengajuan' => (3 - $jlh_pengajuan)]);
});
