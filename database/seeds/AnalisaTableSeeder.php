<?php

use Illuminate\Database\Seeder;
use Thunderlabid\Manajemen\Models\Orang;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Analisa;

use Thunderlabid\Pengajuan\Traits\IDRTrait;

use Carbon\Carbon;

class AnalisaTableSeeder extends Seeder
{
	use IDRTrait;

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('p_analisa')->truncate();
		
		//INIT VARIABLE HELPER
		$faker		= \Faker\Factory::create();

		$jk   		= ['pa', 'pt'];
		$hsl_survei = ['sangat_baik', 'baik', 'cukup_baik', 'tidak_baik', 'buruk'];

		$pengajuan 	= Pengajuan::status('survei')->skip(0)->take(rand(ceil(Pengajuan::status('survei')->count()/4),ceil(Pengajuan::status('survei')->count()/2)))->get();

		//BASIC PENGAJUAN
		foreach ($pengajuan as $key => $value) 
		{
			$data['pengajuan_id']	= $value['id'];
			$data['analis']['nip']	= Orang::first()['nip'];
			$data['analis']['nama']	= Orang::first()['nama'];
			$data['tanggal']		= Carbon::now()->addHours(rand(13,24))->format('d/m/Y H:i');
			$data['character']		= $hsl_survei[rand(0,4)];
			$data['capacity']		= $hsl_survei[rand(0,4)];
			$data['capital']		= $hsl_survei[rand(0,4)];
			$data['condition']		= $hsl_survei[rand(0,4)];
			$data['collateral']		= $hsl_survei[rand(0,4)];
			$data['jenis_pinjaman']	= 'pa';

			$suku_bunga				= (rand(100,500)/1000);

			//kemampuan angsur 
			$k_angs 		= $this->formatMoneyFrom($value['kemampuan_angsur']);
			$p_pinjaman 	= $this->formatMoneyFrom($value['pokok_pinjaman']);

			$angs_bunga  	= $p_pinjaman * $suku_bunga;
			$angs_bulan 	= $k_angs - $angs_bunga;

			$bulan 			= ceil($p_pinjaman/$k_angs);

			//kredit diusulkan
			$kredit_update 	= $bulan * $k_angs;
			$total_bunga 	= $kredit_update - $p_pinjaman;
			$perc_bunga 	= round((($p_pinjaman / max($total_bunga, 1))/100), 2); 
			if($total_bunga==0)
			{
				$perc_bunga = 0;
			}

			$data['jangka_waktu']		= $bulan;
			$data['suku_bunga']			= $perc_bunga;
			$data['limit_angsuran']		= $this->formatMoneyTo($k_angs);
			$data['limit_jangka_waktu']	= $bulan;
			$data['kredit_diusulkan']	= $this->formatMoneyTo($kredit_update - $total_bunga);
			$data['angsuran_pokok']		= $this->formatMoneyTo($p_pinjaman / $data['jangka_waktu']);
			$data['angsuran_bunga']		= $this->formatMoneyTo($total_bunga / $data['jangka_waktu']);

			$analisa = Analisa::create($data);
		}

	}
}
