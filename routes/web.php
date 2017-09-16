<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::domain('localhost')->group(function(){
	Route::get('/', 						['as'	=> 'login', 						'uses' => 'LoginController@login']);
	Route::post('/', 						['as'	=> 'login.post', 					'uses' => 'LoginController@post_login']);
	Route::get('/logout', 					['as'	=> 'logout', 						'uses' => 'LoginController@logout']);
	Route::get('/register', 				['as'	=> 'register', 						'uses' => 'LoginController@register']);
	Route::get('/forget_password', 			['as'	=> 'forget_password', 				'uses' => 'LoginController@forget_password']);
	Route::post('/register', 				['as'	=> 'register.post', 				'uses' => 'LoginController@post_register']);
	Route::post('/forget_password', 		['as'	=> 'forget_password.post', 			'uses' => 'LoginController@post_forget_password']);

	Route::middleware('auth')->group( function() {
		Route::get('/home',			['as'	=> 'home', 	'uses' => 'DashboardController@home']);
	
		Route::prefix('pengajuan')->namespace('Pengajuan')->as('pengajuan.')->group( function() {

			Route::get('/{status}',			['as'	=> 'pengajuan.index', 	'uses' => 'PengajuanController@index']);
			Route::resource('permohonan', 	'PermohonanController');
		});

		Route::any('regensi',	['uses' => 'HelperController@getRegensi', 		'as' => 'regensi.index']);
		Route::any('distrik',	['uses'	=> 'HelperController@getDistrik',		'as' => 'distrik.index']);
		Route::any('desa',		['uses' => 'HelperController@getDesa',			'as' => 'desa.index']);
	});
});


Route::get('/test', function() {
	$api = new Thunderlabid\Instagramapi\API('3290936526.5128893.67c47da60f00415cb35444c693584f2b');
	dd($api->self_followed_by());
});