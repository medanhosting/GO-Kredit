<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Kredit\Models\SuratPeringatan;
use Carbon\Carbon, Config, Auth;

use App\Service\Traits\IDRTrait;

class KeluarkanSP extends Command
{
	use IDRTrait;
	
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gokredit:terbitkansp {--nomor=} {--tanggal=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Terbitkan SP';

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
		$this->info('TERBITKAN SP');
		$this->info("--------------------------------------------------------");
		$this->terbitkan_sp();
		$this->info("\n--------------------------------------------------------");
		$this->info('DONE');
		$this->info("--------------------------------------------------------\n");
	}

	public function terbitkan_sp()
	{
		//checkdays
		$limit 		= Carbon::now()->startOfDay();

		if(!is_null($this->option('tanggal'))){
			$limit 	= Carbon::createFromFormat('d/m/Y', $this->option('tanggal'))->startOfDay();
		}

		$angsuran 	= JadwalAngsuran::HitungTunggakanBeberapaWaktuLalu($limit)->selectraw('count(nth) as total_tunggakan');

		if(!is_null($this->option('nomor'))){
			$angsuran 	= $angsuran->where('nomor_kredit', $this->option('nomor'));
		}
		$angsuran 		= $angsuran->get();

		foreach ($angsuran as $k => $v) {
			$diff_days 	= Carbon::createFromFormat('d/m/Y H:i', $v->tanggal)->diffInDays($limit);
			$tag 		= null;
			if($v->total_tunggakan == 2){
				$tag 	= 'surat_peringatan_1';

				if($diff_days >= 14){
					$tag= 'surat_peringatan_2';
				}
			}elseif($v->total_tunggakan == 3){
				$tag 	= 'surat_peringatan_3';

				if($diff_days >= 14){
					$tag= 'surat_somasi_1';
				}
			}elseif($v->total_tunggakan == 4){
				$tag 	= 'surat_somasi_2';
			}elseif($v->total_tunggakan > 4){
				$tag 	= 'surat_somasi_3';
			}

			if(!is_null($tag)){
				$new_sp 					= SuratPeringatan::where('nomor_kredit', $v['nomor_kredit'])->where('tag', $tag)->first(); 
				if(!$new_sp){
					$new_sp 				= new SuratPeringatan;
					$new_sp->nomor_kredit 	= $v['nomor_kredit'];
					$new_sp->nth 			= $v['nth'];
					$new_sp->tanggal 		= $limit->format('d/m/Y H:i');
					$new_sp->tag 			= $tag;
					$new_sp->karyawan 		= ['nip' => 'GOKREDIT', 'nama' => 'GOKREDIT'];
					$new_sp->save();
				}
			}
		}
	}
}
			
