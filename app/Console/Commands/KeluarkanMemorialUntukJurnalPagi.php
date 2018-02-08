<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;
use Carbon\Carbon, Config, DB;

use App\Service\Traits\IDRTrait;

class KeluarkanMemorialUntukJurnalPagi extends Command
{
	use IDRTrait;
	
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gokredit:jurnalpagi {--tanggal=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Membuat bukti memorial untuk jurnal pagi';

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
		$this->info('JURNAL PAGI');
		$this->info("--------------------------------------------------------");
		$this->info('Membuat Memorial...');
		$this->info("--------------------------------------------------------");
		$this->buat_memorial();
		$this->info("\n--------------------------------------------------------");
		$this->info('DONE');
		$this->info("--------------------------------------------------------\n");
	}

	public function buat_memorial()
	{
		//checkdays
		$tanggal 	= Carbon::now();
		if(!is_null($this->option('tanggal'))){
			$tanggal = Carbon::createfromformat('d/m/Y', $this->option('tanggal'));
		}

		//1. cari angsuran JT today
		//BUG ==> CARI HANYA KREDIT AKTIF
		$angsuran 	= JadwalAngsuran::HitungTunggakanBeberapaWaktuLalu($tanggal)->groupby('nth');
		$angsuran 	= $angsuran->with(['kredit'])->get();

		DB::BeginTransaction();

		try {

			//2. setiap angsuran JT today
			foreach ($angsuran as $k => $v) {
				$nomor_faktur 		= $v['kredit']['kode_kantor'].'.'.$tanggal->startofday()->format('ymd').'-001';

				$bm 				= NotaBayar::where('nomor_faktur', $nomor_faktur)->first();
				if(!$bm){
					$bm 				= new NotaBayar;
					$bm->nomor_faktur	= $nomor_faktur;
				}

				$bm->morph_reference_id 	= $nomor_faktur;
				$bm->morph_reference_tag 	= 'finance';
				$bm->tanggal				= $tanggal->startofday()->format('d/m/Y H:i');
				$bm->karyawan 		= ['nip' => 'GOKREDIT', 'nama' => 'GOKREDIT'];
				$bm->jumlah			= $this->formatMoneyTo(0);
				$bm->jenis 			= 'memorial';
				$bm->save();
				$tanggal 	= $tanggal->endofday();

				$tgl_jt = Carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->endofday();
				//2a. hitung piutang
				if(is_null($v['nomor_faktur']) && str_is($tgl_jt->format('Y-m-d H:is'), $tanggal->format('Y-m-d H:is')) ){
					
					if($v['tunggakan_pokok'] > 0){
						$deskripsi 	= 'Piutang Pokok '.strtoupper($v['kredit']['jenis_pinjaman']).' Jatuh Tempo';
						$piut_pokok	= DetailTransaksi::where('nomor_faktur', $bm->nomor_faktur)->where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->first();
						if(!$piut_pokok){
							$piut_pokok 	= new DetailTransaksi;
						}
						$piut_pokok->nomor_faktur 	= $bm->nomor_faktur;
						$piut_pokok->tag 			= 'pokok';
						$piut_pokok->morph_reference_id		= $v['nomor_kredit'];
						$piut_pokok->morph_reference_tag	= 'kredit';
						$piut_pokok->jumlah 		= $this->formatMoneyTo($v['tunggakan_pokok']);
						$piut_pokok->deskripsi 		= $deskripsi;
						$piut_pokok->save();
					}

					if($v['tunggakan_bunga'] > 0){
						$deskripsi 	= 'Piutang Bunga '.strtoupper($v['kredit']['jenis_pinjaman']).' Jatuh Tempo';
						$piut_bunga	= DetailTransaksi::where('nomor_faktur', $bm->nomor_faktur)->where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->first();
						if(!$piut_bunga){
							$piut_bunga 	= new DetailTransaksi;
						}
						$piut_bunga->nomor_faktur 	= $bm->nomor_faktur;
						$piut_bunga->tag 			= 'bunga';
						$piut_bunga->morph_reference_id		= $v['nomor_kredit'];
						$piut_bunga->morph_reference_tag	= 'kredit';
						$piut_bunga->jumlah 		= $this->formatMoneyTo($v['tunggakan_bunga']);
						$piut_bunga->deskripsi 		= $deskripsi;
						$piut_bunga->save();
					}
				}else{
					//2b. hitung denda
					//total_denda
					$tb 		= $tgl_jt;
					if(!is_null($v['tanggal_bayar'])){
						$tb		= Carbon::createFromformat('d/m/Y H:i', $v['tanggal_bayar']);
					}

					$days 		= $tanggal->diffIndays($tb); 

					if($days > 0){
						$deskripsi 	= 'Piutang Denda Angsuran Ke-'.$v['nth'];

						//previous denda
						$prev_d 	= DetailTransaksi::where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('nomor_faktur', '<>', $bm->nomor_faktur)->sum('jumlah');

						$t_denda 	= ceil($days * ($v['kredit']['persentasi_denda']/100) * $v['tunggakan']) - $prev_d;

						if($t_denda > 0){
							$piut_denda	= DetailTransaksi::where('nomor_faktur', $bm->nomor_faktur)->where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->first();
							if(!$piut_denda){
								$piut_denda 	= new DetailTransaksi;
							}
							$piut_denda->nomor_faktur 	= $bm->nomor_faktur;
							$piut_denda->tag 			= 'denda';
							$piut_denda->morph_reference_id		= $v['nomor_kredit'];
							$piut_denda->morph_reference_tag	= 'kredit';
							$piut_denda->jumlah 		= $this->formatMoneyTo($t_denda);
							$piut_denda->deskripsi 		= $deskripsi;
							$piut_denda->save();
						}
					}
					//2c. hitung titipan
					//pending
				}

				//3. hitung titipan
				$titipan 	= DetailTransaksi::whereIn('tag', ['titipan', 'restitusi_titipan_pokok', 'restitusi_titipan_bunga'])->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->sum('jumlah');

				if($titipan >= $v['tunggakan']){
					$deskripsi 	= 'Pokok Angsuran Ke-'.$v['nth'];
					$rt_pokok	= DetailTransaksi::where('nomor_faktur', $bm->nomor_faktur)->where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->first();
					if(!$rt_pokok){
						$rt_pokok 	= new DetailTransaksi;
					}
					$rt_pokok->nomor_faktur	= $bm->nomor_faktur;
					$rt_pokok->tag 			= 'restitusi_titipan_pokok';
					$rt_pokok->morph_reference_id	= $v['nomor_kredit'];
					$rt_pokok->morph_reference_tag	= 'kredit';
					$rt_pokok->jumlah 			= $this->formatMoneyTo($v['tunggakan_pokok']);
					$rt_pokok->deskripsi 		= $deskripsi;
					$rt_pokok->save();

					$deskripsi 	= 'Bunga Angsuran Ke-'.$v['nth'];
					$rt_bunga	= DetailTransaksi::where('nomor_faktur', $bm->nomor_faktur)->where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->first();
					if(!$rt_bunga){
						$rt_bunga 	= new DetailTransaksi;
					}
					$rt_bunga->nomor_faktur	= $bm->nomor_faktur;
					$rt_bunga->tag 			= 'restitusi_titipan_bunga';
					$rt_bunga->morph_reference_id	= $v['nomor_kredit'];
					$rt_bunga->morph_reference_tag	= 'kredit';
					$rt_bunga->jumlah 			= $this->formatMoneyTo($v['tunggakan_bunga']);
					$rt_bunga->deskripsi 		= $deskripsi;
					$rt_bunga->save();

					//tandai lunas
					$last_t 	= NotaBayar::where('morph_reference_id', $v['kredit']['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('jenis', 'angsuran_sementara')->where('tanggal', '<', $tanggal->format('Y-m-d H:i:s'))->orderby('tanggal', 'desc')->first();

					$new_angs 	= JadwalAngsuran::where('nth', $v['nth'])->where('nomor_kredit', $v['nomor_kredit'])->first();
					$new_angs->tanggal_bayar 	= $last_t->tanggal;
					$new_angs->nomor_faktur 	= $last_t->nomor_faktur;
					$new_angs->save();
				}
			}

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
		}
	}
}
