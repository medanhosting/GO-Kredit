<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your appication. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

#Route::domain('localhost')->group(function(){
	Route::get('/', 						['as'	=> 'login', 				'uses' => 'LoginController@login']);
	Route::post('/', 						['as'	=> 'login.post', 			'uses' => 'LoginController@post_login']);
	Route::get('/logout', 					['as'	=> 'logout', 				'uses' => 'LoginController@logout']);

	Route::get('/forget/password', 			['as'	=> 'forget_password', 		'uses' => 'LoginController@forget_password']);
	Route::post('/register', 				['as'	=> 'register.post', 		'uses' => 'LoginController@post_register']);
	Route::post('/forget/password', 		['as'	=> 'forget_password.post', 	'uses' => 'LoginController@post_forget_password']);

	Route::get('/password', 	['as'	=> 'password.get', 		'uses' => 'LoginController@password_get']);
	Route::post('/password', 	['as'	=> 'password.post', 	'uses' => 'LoginController@password_post']);


	//FRESH START
	Route::middleware(['auth'])->prefix('v2')->group( function() {
		Route::namespace('V2\Dashboard')->group(function(){
			Route::get('/home',		['as'	=> 'home',				'uses' => 'DashboardController@index'])->middleware('pilih_koperasi');
			Route::get('/koperasi',	['as'	=> 'pilih.koperasi',	'uses' => 'DashboardController@koperasi']);
		});
	});
	Route::middleware(['auth', 'pilih_koperasi'])->prefix('v2')->group( function() {
			Route::namespace('V2\Pengajuan')->group(function(){
				Route::resource('simulasi', 		'SimulasiController'); 
				Route::resource('pengajuan', 		'PengajuanController'); 
				Route::resource('putusan', 			'PutusanController'); 
				Route::get('putusan/{id}/print', 	['uses' => 'PutusanController@print', 'as' => 'putusan.print']);
				
				Route::post('assign/{id}', 		['uses' => 'PengajuanController@assign', 	'as' => 'pengajuan.assign']);
				Route::get('pengajuan/ajax', 	['uses' => 'PengajuanController@ajax', 		'as' => 'pengajuan.ajax']);
	
				Route::get('/pengajuan/{id}/print/{mode}',	['as'	=> 'pengajuan.print', 	'uses' => 'PengajuanController@print']);
			});
			
			Route::namespace('V2\Kredit')->group(function(){
				Route::resource('kredit', 			'KreditController'); 
				Route::resource('jaminan',			'MutasiJaminanController'); 
				Route::resource('tunggakan', 		'TunggakanController'); 
				Route::resource('penagihan', 		'PenagihanController');
				Route::resource('angsuran', 		'AngsuranController');

				Route::get('angsuran/{id}/print', 		['uses' => 'AngsuranController@print', 		'as' => 'angsuran.print']);
				Route::get('tunggakan/{id}/print',		['uses' => 'TunggakanController@print',		'as' => 'tunggakan.print']);

				Route::any('angsuran/{id}/validasi', 	['uses' => 'MutasiJaminanController@validasi',	'as' => 'jaminan.validasi']);
			});

			Route::namespace('V2\Finance')->group(function(){
				Route::resource('kas',			'KasController'); 
				Route::resource('akun',			'AkunController'); 
				Route::resource('jurnal',		'JurnalController'); 
				Route::get('kasir/lkh',					['uses' => 'KasirController@lkh',		'as' => 'kasir.lkh']);

				Route::get('kasir/penerimaan/{akun}',	['uses' => 'KasController@penerimaan',	'as' => 'kasir.penerimaan']);
				Route::get('kasir/pengeluaran/{akun}',	['uses' => 'KasController@pengeluaran',	'as' => 'kasir.pengeluaran']);

				Route::get('kasir/penerimaan/tutup/kas',	['uses' => 'KasController@penerimaan_tutup_kas',	'as' => 'kasir.penerimaan.tk']);
				Route::get('kasir/pengeluaran/tutup/kas',	['uses' => 'KasController@pengeluaran_tutup_kas',	'as' => 'kasir.pengeluaran.tk']);

				Route::get('jurnal/{id}/print',		['uses' => 'JurnalController@print',	'as' => 'jurnal.print']);
				Route::get('lkh/print',				['uses' => 'KasirController@print',		'as' => 'kasir.print']);
				
				Route::get('kas/print/{tipe}',		['uses' => 'KasController@print',		'as' => 'kas.print']);
			});
			
		Route::namespace('V2\Kantor')->group(function(){
			Route::resource('kantor', 			'KantorController'); 
			Route::post('kantor/batch', 		['uses' => 'KantorController@batch', 	'as' => 'kantor.batch']);
			Route::resource('karyawan', 		'KaryawanController'); 
			Route::post('karyawan/batch', 		['uses' => 'KaryawanController@batch', 	'as' => 'karyawan.batch']);

			Route::any('kantor/index/ajax', 	['uses' => 'KantorController@ajax', 	'as' => 'kantor.ajax']);
			Route::any('karyawan/index/ajax',	['uses' => 'KaryawanController@ajax', 	'as' => 'karyawan.ajax']);
		});
		Route::namespace('V2\Inspector')->group(function(){
			Route::resource('passcode', 		'PasscodeController');
			Route::resource('audit', 			'AuditController');
		});

		Route::namespace('V2\Test')->group(function(){
			Route::get('display/jurnal', 		['uses' => 'TestController@read_jp', 	'as' => 'jp.index']);
			Route::get('predict/jurnal', 		['uses' => 'TestController@predict_jp', 'as' => 'jp.predict']);
			Route::get('rollback/db', 			['uses' => 'TestController@rollback_db','as' => 'db.rollback']);
		});
	});

	//MAINTAIN MODE

	Route::any('regensi',	['uses' => 'HelperController@getRegensi', 	'as' => 'regensi.index']);
	Route::any('distrik',	['uses'	=> 'HelperController@getDistrik',	'as' => 'distrik.index']);
	Route::any('desa',		['uses' => 'HelperController@getDesa',		'as' => 'desa.index']);

	Route::any('jabatan',	['uses' => 'HelperController@jabatan',		'as' => 'jabatan.index']);
	Route::any('scopes',	['uses' => 'HelperController@scopes',		'as' => 'scopes.index']);

	Route::any('terbilang',	['uses' => 'HelperController@terbilang',	'as' => 'terbilang']);
	
	Route::get('/download/{filename}',	['as' 	=> 'download', 		'uses' => 'DownloadController@download']);
	
	Route::any('upload/image', 		['as' => 'upload.image.store',		'uses' => 'HelperController@storeGambar']);
	Route::any('remove/image',		['as' => 'upload.image.destroy',	'uses' => 'HelperController@destroyGambar']);

	//ajax data log
	Route::any('log/nasabah', 		['as' => 'log.nasabah',		'uses' => 'LogController@nasabah']);
	Route::any('log/bpkb', 			['as' => 'log.bpkb',		'uses' => 'LogController@bpkb']);
	Route::any('log/sertifikat', 	['as' => 'log.sertifikat',	'uses' => 'LogController@sertifikat']);
	
	// privacy policy
	Route::any('privacy/policy',	['as' => 'privacy.policy',	'uses' => 'PageController@privacy_policy']);
#});

