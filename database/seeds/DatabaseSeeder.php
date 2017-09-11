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
    }
}
