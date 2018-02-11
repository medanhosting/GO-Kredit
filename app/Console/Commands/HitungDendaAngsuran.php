<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;
use Carbon\Carbon, Config, DB;

use App\Service\Traits\IDRTrait;
use Thunderlabid\Finance\Models\Traits\FakturTrait;

class HitungDendaAngsuran extends Command
{
	use IDRTrait;
	use FakturTrait;
	
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gokredit:hitungdenda {--nomor=} {--tanggal=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Hitung denda kredit';

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
		$this->info('HITUNG DENDA');
		$this->info("--------------------------------------------------------");
		$this->hitung_denda();
		$this->info("\n--------------------------------------------------------");
		$this->info('DONE');
		$this->info("--------------------------------------------------------\n");
	}

	public function hitung_denda()
	{
		$tanggal 	= Carbon::now();
		if(!is_null($this->option('tanggal'))){
			$tanggal = Carbon::createfromformat('d/m/Y', $this->option('tanggal'));
		}


		//1. cari angsuran JT today
		//BUG ==> CARI HANYA KREDIT AKTIF
		$angsuran 	= JadwalAngsuran::HitungTunggakanBeberapaWaktuLalu($tanggal)->groupby('nth');
		$angsuran 	= $angsuran->with(['kredit'])->get();

		$nomor_faktur 	= null;

		//checkdays
		if(!is_null($this->option('nomor'))){
			$tanggal 	= $tanggal->subdays(1);
			$angsuran 	= JadwalAngsuran::HitungTunggakanBeberapaWaktuLalu($tanggal)->where('nomor_kredit', $this->option('nomor'))->groupby('nth');
			$angsuran 	= $angsuran->with(['kredit'])->get();
			$tanggal 	= $tanggal->adddays(1);
			$nomor_faktur	= NotaBayar::generatenomorfaktur($angsuran[0]['kredit']['kode_kantor'].'.'.$tanggal->startofday()->format('ymd'));
		}


		DB::BeginTransaction();

		try {
			//2. setiap angsuran JT today
			foreach ($angsuran as $k => $v) {
				//generate nofaktur
				$bm					= NotaBayar::where('nomor_faktur', $nomor_faktur)->first();
				if(!$bm){
					$bm 				= new NotaBayar;
					$bm->nomor_faktur 	= NotaBayar::generatenomorfaktur($v['kredit']['kode_kantor'].'.'.$tanggal->startofday()->format('ymd'));
				}
				$bm->morph_reference_id 	= $v['nomor_kredit'];
				$bm->morph_reference_tag 	= 'kredit';
				$bm->tanggal		= $tanggal->startofday()->format('d/m/Y H:i');
				$bm->karyawan 		= ['nip' => 'GOKREDIT', 'nama' => 'GOKREDIT'];
				$bm->jumlah			= $this->formatMoneyTo(0);
				$bm->jenis 			= 'memorial';
				$bm->save();
				
				//2c. Hitung Denda
				//cari selisih hari
				$tgl_jt  	= Carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->subdays(1);
				$tgl_b 		= Carbon::parse($tanggal);
				if(!is_null($v['tanggal_bayar'])){
					$tgl_b 	= Carbon::createfromformat('d/m/Y H:i', $v['tanggal_bayar']);
				}
				$days 		= $tgl_b->diffindays($tgl_jt);
				$deskripsi 	= 'Piutang Denda Angsuran Ke-'.$v['nth'];
				$prev_denda = DetailTransaksi::where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('nomor_faktur', '<>', $bm->nomor_faktur)->sum('jumlah');

				//cari selisih hari
				$denda 		= ceil($days * ($v['kredit']['persentasi_denda']/100) * $v['tunggakan']) - $prev_denda;

				if($denda > 0){
					$piut_denda	= DetailTransaksi::where('nomor_faktur', $bm->nomor_faktur)->where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->first();
					if(!$piut_denda){
						$piut_denda 			= new DetailTransaksi;
					}
					$piut_denda->nomor_faktur 	= $bm->nomor_faktur;
					$piut_denda->tag 			= 'denda';
					$piut_denda->morph_reference_id		= $v['nomor_kredit'];
					$piut_denda->morph_reference_tag	= 'kredit';
					$piut_denda->jumlah 		= $this->formatMoneyTo($denda);
					$piut_denda->deskripsi 		= $deskripsi;
					$piut_denda->save();
				}
			}
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
		}
	}
}
