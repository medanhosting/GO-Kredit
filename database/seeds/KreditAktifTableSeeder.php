<?php

use Illuminate\Database\Seeder;

class KreditAktifTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('k_aktif')->truncate();
		DB::table('k_nota_bayar')->truncate();
		DB::table('k_angsuran_detail')->truncate();
		DB::table('k_penagihan')->truncate();
		DB::table('k_mutasi_jaminan')->truncate();
		DB::table('k_surat_peringatan')->truncate();

		// $kredits 	= \Thunderlabid\Pengajuan\Models\Putusan::where('putusan', 'setuju')->get();

		// foreach ($kredits as $k => $v) {
		// 	event(new \App\Events\AktivasiKredit($v));	
		// }
	}
}
