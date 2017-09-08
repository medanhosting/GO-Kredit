<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Thunderlabid\Socialmedia\Models\Instagram;
use Thunderlabid\Socialmedia\Models\Analysis;
use Thunderlabid\Instagramapi\API;
use Exception;

class IGTrackFollowerCount extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'klepon:IGTrackFollowerCount';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Track Instagram Follower Count';

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
		$this->info('IG: TRACK FOLLOWERS');
		$this->info("--------------------------------------------------------");
		$this->calculate_statistics();
		$this->info("\n--------------------------------------------------------");
		$this->info('DONE');
		$this->info("--------------------------------------------------------\n");
	}

	public function calculate_statistics()
	{
		$bar = $this->output->createProgressBar(Instagram::active()->type('instagram')->count());

		Instagram::active()->chunk(50, function($accounts) use ($bar) {
			foreach ($accounts as $account)
			{
				$ig_api = new API($account->access_token);
				$response = $ig_api->self();
				
				$bar->advance();
				////////////////
				// Store Data //
				////////////////
				$account->statistics()->create([
						'follows'	=> $response->getData()['counts']->follows,
						'followers'	=> $response->getData()['counts']->followed_by,
						'media'		=> $response->getData()['counts']->media,
					]);

			}
		});

		$bar->finish();
	}
}
