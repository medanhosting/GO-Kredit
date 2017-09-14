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

		////////
		// HR //
		////////
		Route::prefix('social-media')->group(function(){
			Route::get('/', 																['as'	=> 'social_media.index', 									'uses' => 'SocialMediaController@index']);
			Route::get('/authenticate/{type}',												['as'	=> 'social_media.authenticate',								'uses' => 'SocialMediaController@authenticate']);

			Route::prefix('instagram')->group(function(){
				Route::get('/{id}',			 												['as'	=> 'social_media.instagram',								'uses' => 'InstagramController@index']);

				// Engage
				Route::get('/{id}/engage',													['as'	=> 'social_media.instagram.engage',							'uses' => 'InstagramController@engage']);
				Route::post('/{id}/engage/post',											['as'	=> 'social_media.instagram.engage.post',					'uses' => 'InstagramController@post_engage']);
				Route::get('/{id}/engage/{engage_id}/activate/{is_active}',					['as'	=> 'social_media.instagram.engage.activate',				'uses' => 'InstagramController@engage_activate']);

				// Media
				Route::get('/{id}/media',													['as'	=> 'social_media.instagram.media',							'uses' => 'InstagramController@media']);
				Route::get('/{id}/tag',														['as'	=> 'social_media.instagram.tag',							'uses' => 'InstagramController@tag']);
				Route::get('/{id}/audience',												['as'	=> 'social_media.instagram.audience',						'uses' => 'InstagramController@audience']);
				Route::get('/{id}/activity',												['as'	=> 'social_media.instagram.activity',						'uses' => 'InstagramController@activity']);
				Route::get('/authenticate/callback', 										['as' 	=> 'social_media.instagram.redirect_authenticate',			'uses' => 'InstagramController@authenticate_callback']);
			});

			// Instagram Redirect URL
		});

	});
});


Route::get('/test', function() {
	$api = new Thunderlabid\Instagramapi\API('3290936526.5128893.67c47da60f00415cb35444c693584f2b');
	dd($api->self_followed_by());
});