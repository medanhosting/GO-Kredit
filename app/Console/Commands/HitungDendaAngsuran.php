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
	protected $signature = 'gokredit:hitungdenda';

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
		$this->checking();
		$this->info("\n--------------------------------------------------------");
		$this->info('DONE');
		$this->info("--------------------------------------------------------\n");
	}

	public function checking()
	{
		$limit 		= Carbon::now()->subDays(Config::get('kredit.batas_pembayaran_angsuran_hari'))->startOfDay();

		$angsuran 	= AngsuranDetail::wherenull('nota_bayar_id')->selectraw('min(tanggal) as tanggal')->selectraw('nomor_kredit')->selectraw('nth')->where('tanggal', '<=', $limit->format('Y-m-d H:i:s'))->groupby(\DB::raw('nomor_kredit, nth'))->get();

		$db 		= Config::get('kredit.denda_perbulan');

		foreach ($angsuran as $k => $v) {
			$now 	= Carbon::createFromFormat('d/m/Y H:i', $v['tanggal'])->diffInMonths($limit) + 1;

			$td		= AngsuranDetail::where('nomor_kredit', $v['nomor_kredit'])->where('tag', 'denda')->where('nth', $v['nth'])->count();

			if($now > $td){
				foreach (range(1, ($now - $td)) as $k2) {
					$d_d 				= new AngsuranDetail;
					$d_d->nomor_kredit 	= $v['nomor_kredit'];
					$d_d->tanggal 		= Carbon::now()->format('d/m/Y H:i');
					$d_d->nth 			= $v['nth'];
					$d_d->tag 			= 'denda';
					$d_d->amount 		= $this->formatMoneyTo($db);
					$d_d->description 	= 'Denda Bulan Ke - '.($k2 + $td);
					$d_d->save();
				}
			}
		}
	}
}
