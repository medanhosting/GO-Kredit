<?php

use Illuminate\Database\Seeder;
use Thunderlabid\Kredit\Models\Rekening;

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
	}
}
