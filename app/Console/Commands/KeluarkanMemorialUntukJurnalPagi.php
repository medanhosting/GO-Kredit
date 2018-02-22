<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;
use Carbon\Carbon, Config, DB;

use App\Service\Traits\IDRTrait;
use App\Service\System\Calculator;

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
			foreach ($angsuran as $k => $v) {
				$flag_bayar 		= false;
				//2. Untuk setiap angsuran JT, buatkan memorial
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
				$bm->jenis 			= 'memorial_jurnal_pagi';
				$bm->save();

				$tomorrow 			= Carbon::parse($tanggal);


				$tgl_jt  	= Carbon::createfromformat('d/m/Y H:i', $v['tanggal']);

				//2b. Timbulkan Piutang
				if(is_null($v['nomor_faktur']) && str_is($tgl_jt->format('Y-m-d'), $tanggal->format('Y-m-d')) ){

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
				}

				//2a. Pakai titipan untuk bayar angsuran JT
				//hitung titipan
				$titipan 	= Calculator::titipanBefore($v['nomor_kredit'], $tomorrow->startofday());

				if($titipan >= $v['tunggakan']){
					if($v['tunggakan_pokok'] > 0){
						//bayar pokok
						$deskripsi 	= 'Pokok Angsuran Ke-'.$v['nth'];
						$pokok		= DetailTransaksi::where('nomor_faktur', $bm->nomor_faktur)->where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->first();
						if(!$pokok){
							$pokok 	= new DetailTransaksi;
						}

						$pokok->nomor_faktur			= $bm->nomor_faktur;
						$pokok->tag 					= 'restitusi_pokok';
						$pokok->morph_reference_id	= $v['nomor_kredit'];
						$pokok->morph_reference_tag	= 'kredit';
						$pokok->jumlah 				= $this->formatMoneyTo($v['tunggakan_pokok']);
						$pokok->deskripsi 			= $deskripsi;
						$pokok->save();
					}

					if($v['tunggakan_bunga'] > 0){
						//bayar bunga
						$deskripsi 	= 'Bunga Angsuran Ke-'.$v['nth'];
						$bunga		= DetailTransaksi::where('nomor_faktur', $bm->nomor_faktur)->where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->first();
						if(!$bunga){
							$bunga 	= new DetailTransaksi;
						}

						$bunga->nomor_faktur			= $bm->nomor_faktur;
						$bunga->tag 					= 'restitusi_bunga';
						$bunga->morph_reference_id	= $v['nomor_kredit'];
						$bunga->morph_reference_tag	= 'kredit';
						$bunga->jumlah 				= $this->formatMoneyTo($v['tunggakan_bunga']);
						$bunga->deskripsi 			= $deskripsi;
						$bunga->save();
					}

					//UPDATE PELUNASAN
					$nota_titipan 	= NotaBayar::where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->Where('tanggal', '<', $tanggal->format('Y-m-d H:i:s'))->where('jenis', 'angsuran_sementara')->orderby('tanggal', 'desc')->first();

					$angs_lunas 	= JadwalAngsuran::where('nomor_kredit', $v['nomor_kredit'])->where('nth', $v['nth'])->first();
					$angs_lunas->nomor_faktur 	= $nota_titipan->nomor_faktur;
					$angs_lunas->tanggal_bayar	= $tanggal->format('d/m/Y H:i');
					$angs_lunas->save();
					$flag_bayar 	= true;
				}

				//2c. Hitung Denda
				//cari selisih hari
				$tgl_jtt  	= Carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->subdays(1);
				$tgl_b 		= Carbon::parse($tanggal);
				if(!is_null($v['tanggal_bayar'])){
					$tgl_b 	= Carbon::createfromformat('d/m/Y H:i', $v['tanggal_bayar']);
				}
				$days 		= $tgl_b->diffindays($tgl_jtt);

				$deskripsi 	= 'Piutang Denda Angsuran Ke-'.$v['nth'];
				$prev_denda = DetailTransaksi::where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('nomor_faktur', '<>', $bm->nomor_faktur)->sum('jumlah');

				//cari selisih hari
				$denda 		= ceil($days * ($v['kredit']['persentasi_denda']/100) * $v['tunggakan']) - $prev_denda;

				if($denda > 0 && !$flag_bayar){
					$piut_denda	= DetailTransaksi::where('nomor_faktur', $bm->nomor_faktur)->where('deskripsi', $deskripsi)->where('morph_reference_id', $v['nomor_kredit'])->where('morph_reference_tag', 'kredit')->first();
					if(!$piut_denda){
						$piut_denda 			= new DetailTransaksi;
					}
					\Log::info(1);
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
