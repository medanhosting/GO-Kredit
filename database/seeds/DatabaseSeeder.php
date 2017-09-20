<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(ManajemenTableSeeder::class);
		$this->call(PengajuanTableSeeder::class);
		$this->call(SurveiTableSeeder::class);
		$this->call(AnalisaTableSeeder::class);
		$this->call(PutusanTableSeeder::class);
		$this->call(PengajuanDuplicateTableSeeder::class);
		// $this->call(IndonesiaTableSeeder::class);
	}
}
