<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Thunderlabid\Kredit\Models\Angsuran;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Carbon\Carbon, Config;

use App\Service\Traits\IDRTrait;

class HitungDendaAngsuran extends Command
{
	use IDRTrait;
	
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gokredit:hitungdenda {--nomor=}';

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
		//checkdays
		$limit 		= Carbon::now()->subDays(Config::get('kredit.batas_pembayaran_angsuran_hari'))->endofday();
		$angsuran 	= AngsuranDetail::HitungTunggakanBeberapaWaktuLalu($limit)->with(['kredit', 'notabayar'])->groupby('nth');

		if(!is_null($this->option('nomor'))){
			$angsuran 	= $angsuran->where('nomor_kredit', $this->option('nomor'));
		}
		$angsuran 	= $angsuran->get();

		foreach ($angsuran as $k => $v) {
			//case nota bayar terlambat
			if(is_null($v->nota_bayar_id)){
				$d_passed 	= Carbon::parse($limit)->diffIndays(Carbon::createfromformat('d/m/Y H:i', $v->tanggal));
			}else{
				$d_passed 	= Carbon::createfromformat('d/m/Y H:i', $v->notabayar->tanggal)->diffIndays(Carbon::createfromformat('d/m/Y H:i', $v->tanggal));
			}

			$db 	= ($v->tunggakan * $v->kredit->persentasi_denda * ($d_passed-Config::get('kredit.batas_pembayaran_angsuran_hari')))/100;

			$td		= AngsuranDetail::where('nomor_kredit', $v['nomor_kredit'])->where('tag', 'denda')->where('nth', $v['nth'])->first();

			if($db != $this->formatMoneyFrom($td->amount)){
				if(!$td){
					$td 			= new AngsuranDetail;
				}
				$td->nomor_kredit 	= $v['nomor_kredit'];
				$td->tanggal 		= Carbon::now()->format('d/m/Y H:i');
				$td->nth 			= $v['nth'];
				$td->tag 			= 'denda';
				$td->amount 		= $this->formatMoneyTo($db);
				$td->description 	= 'Denda Angsuran Ke - '.$v->nth;
				$td->save();
			}
		}
	}
}
