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

Route::middleware('api')->namespace('API')->group(function(){
	Route::any('/me',							['uses' => 'UserController@show']);
	Route::any('/simulasi/{mode}',				['uses' => 'PermohonanController@simulasi']);
	Route::any('/permohonan/store',				['uses' => 'PermohonanController@store']);
	Route::any('/permohonan/index',				['uses' => 'PermohonanController@index']);
	
	//UPLOAD FILE
	Route::any('/simpan/gambar',	['uses' => 'UploadGambarController@store']);
	Route::any('/hapus/gambar',		['uses' => 'UploadGambarController@destroy']);

	//SURVEI
	Route::any('/survei/index',										['uses' => 'SurveiController@index']);
	Route::any('/survei/{pengajuan_id}/foto/{survei_detail_id}',	['uses' => 'SurveiController@simpan_foto']);
	
	//NOTABAYAR
	Route::get('/nota/bayar/index',				['uses' => 'NotaBayarController@index']);
	Route::get('/nota/bayar/show/{id}',			['uses' => 'NotaBayarController@show']);

	//PENAGIHAN
});


Route::any('/pengaturan', function (Request $request) 
{
	if(Auth::user() && !is_null(Auth::user()['nip']))
	{
		$data 	= ['minimum_pengajuan' => 2500000, 'minimum_shgb' => Carbon\Carbon::now()->format('Y'), 'minimum_bpkb' => Carbon\Carbon::now()->subYears(25)->format('Y'), 'remain_pengajuan' => 1, 'max_jaminan_kendaraan' => 2, 'max_jaminan_tanah_dan_bangunan' => 3];

		return response()->json(['status' => 1, 'data' => $data, 'error' => ['message' => []]]);
	}

	$phone 			= $request->get('mobile');
	$jlh_pengajuan	= \Thunderlabid\Pengajuan\Models\Pengajuan::status('permohonan')->where('nasabah->telepon', $phone['telepon'])->count();

	$data 	= ['minimum_pengajuan' => 2500000, 'minimum_shgb' => Carbon\Carbon::now()->format('Y'), 'minimum_bpkb' => Carbon\Carbon::now()->subYears(25)->format('Y'), 'remain_pengajuan' => (3 - $jlh_pengajuan), 'max_jaminan_kendaraan' => 2, 'max_jaminan_tanah_dan_bangunan' => 3];

	return response()->json(['status' => 1, 'data' => $data, 'error' => ['message' => []]]);
});


Route::group(['namespace' => 'OpenSource'], function(){
	Route::any('/indonesia',	['uses' => 'IndonesiaController@index']);
});
