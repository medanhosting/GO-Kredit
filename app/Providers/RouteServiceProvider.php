<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

use Config;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot()
	{
		//

		parent::boot();
	}

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function register()
	{
		$menu	= [
			'pengajuan'	=> ['operasional', 'permohonan', 'survei', 'analisa', 'putusan', 'realisasi', 'pencairan'], 
			'kredit'	=> ['operasional', 'angsuran', 'denda', 'tagihan', 'jaminan', 'tunggakan'], 
			'keuangan'	=> ['keuangan', 'akun'], 
			'kantor'	=> ['holding'], 
			'inspektor'	=> ['holding'], 
			'test'		=> ['holding'],

			'pengajuan.pengajuan'	=> ['operasional', 'permohonan', 'survei', 'analisa', 'putusan'], 
			'pengajuan.putusan'		=> ['operasional', 'realisasi', 'pencairan'], 
			'pengajuan.simulasi'	=> ['permohonan'], 
			'pengajuan.permohonan'	=> ['permohonan'], 
			
			'kredit.kredit'			=> ['operasional', 'angsuran', 'denda', 'tagihan', 'jaminan'], 
			'kredit.tunggakan'		=> ['operasional', 'tunggakan'], 
			'kredit.jaminan'		=> ['operasional', 'jaminan'], 
			'kredit.tagihan'		=> ['operasional', 'tagihan'], 
			'kredit.register'		=> ['operasional', 'angsuran'], 
			
			'keuangan.lkh'		=> ['operasional', 'keuangan'], 
			'keuangan.jurnal'	=> ['operasional', 'keuangan'], 
			'keuangan.kas'		=> ['kas'], 
			'keuangan.akun'		=> ['akun'], 
			
			'kantor.kantor'		=> ['kantor'], 
			'kantor.karyawan'	=> ['karyawan'], 
			
			'inspektor.passcode'	=> ['passcode'], 
			'inspektor.audit'		=> ['audit'], 
			
			'test.jurnal'		=> ['holding'], 
			'test.prediksi'		=> ['holding'], 
			'test.rollback'		=> ['holding'], 
			
			'pengajuan.pengajuan.permohonan'	=> ['operasional', 'permohonan'], 
			'pengajuan.pengajuan.survei'		=> ['operasional', 'survei'], 
			'pengajuan.pengajuan.analisa'		=> ['operasional', 'analisa'], 
			'pengajuan.pengajuan.putusan'		=> ['operasional', 'putusan'],

			'pengajuan.pengajuan.*.permohonan'	=> ['operasional', 'permohonan', 'survei', 'analisa', 'putusan'], 
			'pengajuan.pengajuan.*.survei'		=> ['operasional', 'survei', 'analisa', 'putusan'], 
			'pengajuan.pengajuan.*.analisa'		=> ['operasional', 'analisa', 'putusan'], 
			'pengajuan.pengajuan.*.putusan'		=> ['operasional', 'putusan'],

			'pengajuan.putusan.setuju'			=> ['operasional', 'realisasi', 'pencairan'], 
			'pengajuan.putusan.tolak'			=> ['operasional'], 

			'pengajuan.putusan.setuju.legalitas'	=> ['operasional', 'realisasi'], 
			'pengajuan.putusan.setuju.pencairan'	=> ['operasional', 'pencairan'], 
			'pengajuan.putusan.setuju.setoran'		=> ['operasional', 'pencairan'], 

			'kredit.kredit.aktif.angsuran'		=> ['operasional', 'angsuran'], 
			'kredit.kredit.aktif.denda'			=> ['operasional', 'denda', 'restitusi', 'validasi'], 
			'kredit.kredit.aktif.denda.bayar'		=> ['denda'], 
			'kredit.kredit.aktif.denda.restitusi'	=> ['restitusi', 'validasi'], 
			'kredit.kredit.aktif.tagihan'		=> ['operasional', 'tagihan'], 
			'kredit.kredit.aktif.jaminan'		=> ['operasional', 'jaminan'], 
		];

		Config::set('acl.menu', $menu); 
	}
	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map()
	{
		$this->mapApiRoutes();

		$this->mapWebRoutes();

		//
	}

	/**
	 * Define the "web" routes for the application.
	 *
	 * These routes all receive session state, CSRF protection, etc.
	 *
	 * @return void
	 */
	protected function mapWebRoutes()
	{
		Route::middleware('web')
			 ->namespace($this->namespace)
			 ->group(base_path('routes/web.php'));
	}

	/**
	 * Define the "api" routes for the application.
	 *
	 * These routes are typically stateless.
	 *
	 * @return void
	 */
	protected function mapApiRoutes()
	{
		Route::prefix('api')
			 // ->middleware('api')
			 ->namespace($this->namespace)
			 ->group(base_path('routes/api.php'));
	}
}
