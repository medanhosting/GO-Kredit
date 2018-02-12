<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\MutasiJaminan;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Kredit\Models\SuratPeringatan;
use Thunderlabid\Kredit\Models\PermintaanRestitusi;
use Thunderlabid\Finance\Models\Jurnal;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;
use Carbon\Carbon, Config, Auth;

use App\Service\Traits\IDRTrait;

class RollbackTransaction extends Command
{
	use IDRTrait;
	
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gokredit:rollbacktransaction {--tanggal=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Rollback Transaksi';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->info("--------------------------------------------------------");
		$this->info('ROLLBACK TRANSACTION');
		$this->info("--------------------------------------------------------");
		$this->rollback_transaction();
		$this->info("\n--------------------------------------------------------");
		$this->info('DONE');
		$this->info("--------------------------------------------------------\n");
	}

	public function rollback_transaction()
	{
		//checkdays
		$tanggal 	= Carbon::now();
		if(!is_null($this->option('tanggal'))){
			$tanggal = Carbon::createfromformat('d/m/Y', $this->option('tanggal'));
		}

		//hapus detail transaksi
		$dtrans 	= DetailTransaksi::wherehas('notabayar', function($q)use($tanggal){
			$q->where('tanggal', '>=', $tanggal->startofday()->format('Y-m-d H:i:s'));
		})->delete();

		//hapus nota bayar
		$notabayar 	= NotaBayar::where('tanggal', '>=', $tanggal->startofday()->format('Y-m-d H:i:s'))->delete();

		//hapus jurnal
		$jurnal 	= Jurnal::where('tanggal', '>=', $tanggal->startofday()->format('Y-m-d H:i:s'))->delete();

		//hapus jadwal angsuran
		$jadwal 	= JadwalAngsuran::where('tanggal_bayar', '>=', $tanggal->startofday()->format('Y-m-d H:i:s'))->update(['nomor_faktur' => null, 'tanggal_bayar' => null]);

		//hapus sp
		$sp 		= SuratPeringatan::where('tanggal', '>=', $tanggal->startofday()->format('Y-m-d H:i:s'))->delete();

		//hapus penagihan
		$penagihan 	= Penagihan::where('tanggal', '>=', $tanggal->startofday()->format('Y-m-d H:i:s'))->delete();

		//hapus restitusi
		$restitusi 	= PermintaanRestitusi::where('tanggal', '>=', $tanggal->startofday()->format('Y-m-d H:i:s'))->delete();

		//hapus mutasi
		$mutasi 	= MutasiJaminan::where('tanggal', '>=', $tanggal->startofday()->format('Y-m-d H:i:s'))->delete();
	}
}
			
