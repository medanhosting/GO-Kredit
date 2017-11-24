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

		$angsuran 	= Angsuran::wherenull('paid_at')->where('issued_at', '<', $limit->format('Y-m-d H:i:s'))->get();

		$denda_bulanan 	= Config::get('kredit.denda_perbulan');

		foreach ($angsuran as $k => $v) {
			$diff_months 	= Carbon::now()->diffInMonths($limit) + 1;

			$a_detail 		= AngsuranDetail::where('angsuran_id', $v['id'])->where('tag', 'denda')->count();

			foreach (range(1, ($diff_months - $a_detail)) as $k2) {
				$bulan 		= Carbon::createFromFormat('d/m/Y H:i', $v->issued_at)->addmonths($a_detail + $k2);

				$new_d 		= new AngsuranDetail;
				$new_d->angsuran_id 	= $v['id'];
				$new_d->tag 			= 'denda';
				$new_d->amount 			= $this->formatMoneyTo($denda_bulanan);
				$new_d->description 	= 'Denda bulan ke - '.$bulan->format('N').' tahun '.$bulan->format('Y');
				$new_d->save();
			}
		}
	}
}
