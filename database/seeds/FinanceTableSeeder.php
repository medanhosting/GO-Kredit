<?php

use Illuminate\Database\Seeder;
use Thunderlabid\Kredit\Models\Rekening;

class FinanceTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('f_account')->truncate();
		DB::table('f_coa')->truncate();
		DB::table('f_transaction_detail')->truncate();
	}
}
