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

	// Route::get('/pengaturan', function (Request $request) 
	// {
	// 	if($request->has('nip_karyawan'))
	// 	{
	// 		return \App\Service\Helpers\API\JSend::success(['minimum_pengajuan' => 2500000, 'minimum_shgb' => Carbon\Carbon::now()->format('Y'), 'remain_pengajuan' => 1])->asArray();
	// 	}

	// 	$mobile_id  = $request->get('id');

	// 	$total 		= \App\Domain\Pengajuan\Models\Pengajuan::whereHas('hp', function($q)use($mobile_id){$q->where('mobile_id', $mobile_id);})->status('pengajuan')->count();

	// 	return \App\Service\Helpers\API\JSend::success(['minimum_pengajuan' => 2500000, 'minimum_shgb' => Carbon\Carbon::now()->format('Y'), 'remain_pengajuan' => (3 - $total)])->asArray();
	// });
});


Route::get('/pengaturan', function (Request $request) 
{
	if($request->has('nip_karyawan'))
	{
		return Response::json(['minimum_pengajuan' => 2500000, 'minimum_shgb' => Carbon\Carbon::now()->format('Y'), 'remain_pengajuan' => 1]);
	}

	return Response::json(['minimum_pengajuan' => 2500000, 'minimum_shgb' => Carbon\Carbon::now()->format('Y'), 'remain_pengajuan' => 3]);
});