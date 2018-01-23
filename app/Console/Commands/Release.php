<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;

class Release extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gokredit:release';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Release next iteration';

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
		$this->info('RELEASE V1');
		$this->info("--------------------------------------------------------");
		$this->v1();
		$this->info("\n--------------------------------------------------------");
		$this->info('DONE');
		$this->info("--------------------------------------------------------\n");

		$this->info("--------------------------------------------------------");
		$this->info('RELEASE V2');
		$this->info("--------------------------------------------------------");
		$this->v2();
		$this->info("\n--------------------------------------------------------");
		$this->info('DONE');
		$this->info("--------------------------------------------------------\n");
	}

	public function v1()
	{
		Artisan::call('migrate:refresh', ['--path' => 'database/migrations/1.0.0/']);
	}

	public function v2()
	{
		Artisan::call('migrate:refresh', ['--path' => 'database/migrations/2.0.0/']);
	}
}