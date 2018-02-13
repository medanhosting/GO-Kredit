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

		Event::listen('Thunderlabid\Kredit\Events\NotaBayar\NotaBayarCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\NotaBayar\NotaBayarUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\NotaBayar\NotaBayarDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');

		Event::listen('Thunderlabid\Kredit\Events\SuratPeringatan\SuratPeringatanCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\SuratPeringatan\SuratPeringatanUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\SuratPeringatan\SuratPeringatanDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');

		Event::listen('Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');

		Event::listen('Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');

		Event::listen('Thunderlabid\Kredit\Events\Jaminan\JaminanCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\Jaminan\JaminanUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\Jaminan\JaminanDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');

		Event::listen('Thunderlabid\Kredit\Events\Penagihan\PenagihanCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\Penagihan\PenagihanUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\Penagihan\PenagihanDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');


		Event::listen('Thunderlabid\Kredit\Events\PermintaanRestitusi\PermintaanRestitusiCreating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\PermintaanRestitusi\PermintaanRestitusiUpdating', 'Thunderlabid\Kredit\Listeners\Saving');
		Event::listen('Thunderlabid\Kredit\Events\PermintaanRestitusi\PermintaanRestitusiDeleting', 'Thunderlabid\Kredit\Listeners\Deleting');

		//////////////////////////
		// Buat Jadwal Angsuran //
		//////////////////////////
		Event::listen('Thunderlabid\Kredit\Events\Aktif\AktifCreated', 'Thunderlabid\Kredit\Listeners\BuatJadwalAngsuran');

		//////////////////
		// Hitung Denda //
		//////////////////
		Event::listen('Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailCreated', 'Thunderlabid\Kredit\Listeners\RecalculateDenda');
		Event::listen('Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailUpdated', 'Thunderlabid\Kredit\Listeners\RecalculateDenda');
	}

	public function register()
	{
		Config::set('kredit.biaya_kolektor', 50000);
		Config::set('kredit.selisih_penagihan_hari', 5);
		Config::set('kredit.batas_pembayaran_angsuran_hari', 3);
		Config::set('kredit.denda_perbulan', 30000);
	}
}