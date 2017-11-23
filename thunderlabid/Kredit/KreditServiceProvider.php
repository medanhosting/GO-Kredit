<?php

namespace Thunderlabid\Kredit;

use Illuminate\Support\ServiceProvider;
use Event, Config;

class KreditServiceProvider extends ServiceProvider
{
	public function boot()
	{
		////////////////
		// Validation //
		////////////////
		Event::listen('Thunderlabid\Kredit\Events\Aktif\AktifCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\Aktif\AktifUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\Aktif\AktifDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');

		Event::listen('Thunderlabid\Kredit\Events\Angsuran\AngsuranCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\Angsuran\AngsuranUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\Angsuran\AngsuranDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');

		Event::listen('Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');

		Event::listen('Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');

		Event::listen('Thunderlabid\Kredit\Events\Penagihan\PenagihanCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\Penagihan\PenagihanUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\Penagihan\PenagihanDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');

		///////////////////////////////
		// Pembebanan Biaya Kolektor //
		///////////////////////////////
		Event::listen('Thunderlabid\Kredit\Events\Penagihan\PenagihanCreated', 'Thunderlabid\Kredit\Listeners\BebankanBiayaKolektor');

		//////////////////////////
		// Buat Jadwal Angsuran //
		//////////////////////////
		Event::listen('Thunderlabid\Kredit\Events\Aktif\AktifCreated', 'Thunderlabid\Kredit\Listeners\BuatJadwalAngsuran');

		/////////////////////////
		// Tandai Kredit Lunas //
		/////////////////////////
		Event::listen('Thunderlabid\Kredit\Events\Angsuran\AngsuranCreated', 'Thunderlabid\Kredit\Listeners\TandaiKreditLunas');
		Event::listen('Thunderlabid\Kredit\Events\Angsuran\AngsuranUpdated', 'Thunderlabid\Kredit\Listeners\TandaiKreditLunas');

		///////////////////////////
		// Tandai Jaminan Keluar //
		///////////////////////////
		Event::listen('Thunderlabid\Kredit\Events\Angsuran\AngsuranCreated', 'Thunderlabid\Kredit\Listeners\TandaiJaminanKeluar');
		Event::listen('Thunderlabid\Kredit\Events\Angsuran\AngsuranUpdated', 'Thunderlabid\Kredit\Listeners\TandaiJaminanKeluar');
	}

	public function register()
	{
		Config::set('kredit.biaya_kolektor', 50000);
		Config::set('kredit.batas_pembayaran_angsuran_hari', 7);
		Config::set('kredit.denda_perbulan', 30000);
	}
}