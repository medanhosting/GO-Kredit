<?php

use Illuminate\Database\Seeder;
use Thunderlabid\Manajemen\Models\Orang;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Analisa;

use Thunderlabid\Pengajuan\Traits\IDRTrait;

use App\Http\Service\Policy\PerhitunganBunga;

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

		$pengajuan 	= Pengajuan::status('survei')->skip(0)->take(rand(ceil(Pengajuan::status('survei')->count()/2),ceil(Pengajuan::status('survei')->count()/1)))->get();

		//BASIC PENGAJUAN
		foreach ($pengajuan as $key => $value) 
		{
			$data['pengajuan_id']	= $value['id'];
			$data['analis']['nip']	= Orang::first()['nip'];
			$data['analis']['nama']	= Orang::first()['nama'];
			$data['tanggal']		= Carbon::now()->subdays(rand(110,119))->format('d/m/Y H:i');
			$data['character']		= $hsl_survei[rand(0,4)];
			$data['capacity']		= $hsl_survei[rand(0,4)];
			$data['capital']		= $hsl_survei[rand(0,4)];
			$data['condition']		= $hsl_survei[rand(0,4)];
			$data['collateral']		= $hsl_survei[rand(0,4)];
			$data['jenis_pinjaman']	= 'pa';

			$hitung 	= new PerhitunganBunga($value['pokok_pinjaman'], $value['kemampuan_angsur']);
			$hitung 	= $hitung->pa();

			$data['jangka_waktu']		= $hitung['lama_angsuran'];
			$data['suku_bunga']			= $hitung['bunga_per_bulan'];
			$data['limit_angsuran']		= $hitung['kemampuan_angsur'];
			$data['limit_jangka_waktu']	= $hitung['lama_angsuran'];
			$data['kredit_diusulkan']	= $hitung['pokok_pinjaman'];
			$data['angsuran_pokok']		= $hitung['angsuran'][1]['angsuran_pokok'];
			$data['angsuran_bunga']		= $hitung['angsuran'][1]['angsuran_bunga'];

			$analisa = Analisa::create($data);
		}

	}
}
