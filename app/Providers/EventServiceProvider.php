<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
	];

	protected $subscribe = [
	];

	/**
	 * Register any events for your application.
	 *
	 * @return void
	 */
	public function boot()
	{
		parent::boot();

		//SURVEI DAN PENGAJUAN BRIDGE UPDATE STATUS
		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanCreated', 'App\Listeners\SimpanStatusDoingPermohonan');

		Event::listen('Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailCreated', 'App\Listeners\SimpanStatusDoingSurvei');
		Event::listen('Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailUpdated', 'App\Listeners\SimpanStatusDoingSurvei');
		Event::listen('Thunderlabid\Survei\Events\AssignedSurveyor\AssignedSurveyorCreated', 'App\Listeners\SimpanStatusTodoSurvei');
		Event::listen('Thunderlabid\Survei\Events\AssignedSurveyor\AssignedSurveyorUpdated', 'App\Listeners\SimpanStatusTodoSurvei');

		Event::listen('Thunderlabid\Survei\Events\SurveiFoto\SurveiFotoCreated', 'App\Listeners\UpdatingSurveiDetail');
		Event::listen('Thunderlabid\Survei\Events\SurveiFoto\SurveiFotoUpdated', 'App\Listeners\UpdatingSurveiDetail');

		Event::listen('Thunderlabid\Pengajuan\Events\Analisa\AnalisaCreated', 'App\Listeners\SimpanStatusDoingAnalisa');
		Event::listen('Thunderlabid\Pengajuan\Events\Analisa\AnalisaUpdated', 'App\Listeners\SimpanStatusDoingAnalisa');

		Event::listen('Thunderlabid\Pengajuan\Events\Putusan\PutusanCreated', 'App\Listeners\SimpanStatusDoingPutusan');
		Event::listen('Thunderlabid\Pengajuan\Events\Putusan\PutusanUpdated', 'App\Listeners\SimpanStatusDoingPutusan');
		// Event::listen('Thunderlabid\Pengajuan\Events\Putusan\PutusanCreated', 'App\Listeners\GenerateLegalitasRealisasi');
		// Event::listen('Thunderlabid\Pengajuan\Events\Putusan\PutusanUpdated', 'App\Listeners\GenerateLegalitasRealisasi');
		
		Event::listen('App\Events\PengajuanExpired', 'App\Listeners\SimpanStatusDoneExpire');

		//DELETE PENGAJUAN
		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanDeleted', 'App\Listeners\SimpanStatusDoneVoid');

		//DELETE KANTOR
		Event::listen('Thunderlabid\Manajemen\Events\Kantor\KantorDeleting', 'App\Listeners\BukanHolding');
		Event::listen('Thunderlabid\Manajemen\Events\Kantor\KantorDeleting', 'App\Listeners\TidakAdaKredit');

		//LISTEN LOG
		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanCreated', 'App\Listeners\SimpanLogNasabah');
		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanUpdated', 'App\Listeners\SimpanLogNasabah');

		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanCreated', 'App\Listeners\SimpanLogBPKB');
		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanUpdated', 'App\Listeners\SimpanLogBPKB');

		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanCreated', 'App\Listeners\SimpanLogSHM');
		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanUpdated', 'App\Listeners\SimpanLogSHM');

		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanCreated', 'App\Listeners\SimpanLogSHGB');
		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanUpdated', 'App\Listeners\SimpanLogSHGB');

		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanCreated', 'App\Listeners\SimpanLogKredit');
		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanUpdated', 'App\Listeners\SimpanLogKredit');
		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanCreated', 'App\Listeners\SimpanLogKreditViaJaminan');
		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanUpdated', 'App\Listeners\SimpanLogKreditViaJaminan');

		//UPDATING IS LAMA JAMINAN
		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanCreated', 'App\Listeners\UpdatingJaminanIsLama');
		Event::listen('Thunderlabid\Pengajuan\Events\Jaminan\JaminanUpdated', 'App\Listeners\UpdatingJaminanIsLama');
		//UPDATING IS LAMA NASABAH
		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanCreated', 'App\Listeners\UpdatingNasabahIsLama');
		Event::listen('Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanUpdated', 'App\Listeners\UpdatingNasabahIsLama');


		//SURVEILOKASI
		Event::listen('Thunderlabid\Survei\Events\Survei\SurveiCreated', 'App\Listeners\UpdateSurveiLokasi');
		Event::listen('Thunderlabid\Survei\Events\Survei\SurveiUpdated', 'App\Listeners\UpdateSurveiLokasi');
	
		//REMOVE SURVEI LOKASI ONCE STATUS CREATED
		Event::listen('Thunderlabid\Pengajuan\Events\Status\StatusCreated', 'App\Listeners\RemoveSurveiLokasi');
		Event::listen('Thunderlabid\Pengajuan\Events\Status\StatusUpdated', 'App\Listeners\RemoveSurveiLokasi');

		//SURVEI DETAIL COLLATERAL AUTO CREATE
		Event::listen('Thunderlabid\Survei\Events\Survei\SurveiCreated', 'App\Listeners\CreateCollateralSurveiDetail');

		//////////////////////////
		// Tandai Jaminan Masuk //
		//////////////////////////
		Event::listen('Thunderlabid\Kredit\Events\Aktif\AktifCreated', 'App\Listeners\TandaiJaminanMasuk');

		///////////////////////////////
		// Aktivasi Kredit Disetujui //
		///////////////////////////////
		Event::listen('Thunderlabid\Finance\Events\NotaBayar\NotaBayarCreated', 'App\Listeners\AktivasiKreditDisetujui');
		Event::listen('Thunderlabid\Finance\Events\NotaBayar\NotaBayarUpdated', 'App\Listeners\AktivasiKreditDisetujui');

		////////////////////////////////
		// AUTO CREATED BASIC ACCOUNT //
		////////////////////////////////
		Event::listen('Thunderlabid\Manajemen\Events\Kantor\KantorCreated', 'App\Listeners\AutoCreateAccount');
		Event::listen('Thunderlabid\Manajemen\Events\Kantor\KantorUpdated', 'App\Listeners\AutoCreateAccount');

		////////////////////////////
		// Bantu Kasir Jurnaling  //
		////////////////////////////
		Event::listen('Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiCreated', 'App\Listeners\AutoJournal');
		Event::listen('Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiUpdated', 'App\Listeners\AutoJournal');

		////////////////////////////////
		// Otomatis Isi Tanggal Bayar //
		////////////////////////////////
		Event::listen('Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranCreating', 'App\Listeners\AutoFillTanggalBayar');
		Event::listen('Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranUpdating', 'App\Listeners\AutoFillTanggalBayar');

		///////////////////////////////////////////
		// Cadangkan Legalitas Yang Tidak Perlu  //
		///////////////////////////////////////////
		Event::listen('Thunderlabid\Pengajuan\Events\Status\StatusCreated', 'App\Listeners\CadangkanLegalitas');
		Event::listen('Thunderlabid\Pengajuan\Events\Status\StatusUpdated', 'App\Listeners\CadangkanLegalitas');

		///////////////////////////
		// Tandai Jaminan Keluar //
		///////////////////////////
		Event::listen('Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiCreated', 'App\Listeners\TandaiJaminanKeluar');
		Event::listen('Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiUpdated', 'App\Listeners\TandaiJaminanKeluar');

	}
}
