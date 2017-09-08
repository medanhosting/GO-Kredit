<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Thunderlabid\Socialmedia\Models\Instagram;
use Thunderlabid\Socialmedia\Models\IGMedia;
use Thunderlabid\Instagramapi\API;
use Exception;

class IGAnalyzeMedia extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'klepon:IGAnalyzeMedia';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Analyze media posted by users, finding top performing media to growth';

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
		$this->do();
		$this->info("\n--------------------------------------------------------");
		$this->info('DONE');
		$this->info("--------------------------------------------------------\n");
	}

	public function do()
	{
		$bar = $this->output->createProgressBar(Instagram::active()->type('instagram')->count());

		Instagram::active()->chunk(50, function($accounts) use ($bar) {
			foreach ($accounts as $account)
			{
				$this->info(" * Analyzing " . $account->name);
				$ig_api = new API($account->access_token);

				/////////////////////////
				// Get Media & Analyze //
				/////////////////////////
				$max_id = null;
				do {
					$response = $ig_api->self_media($max_id);

					foreach ($response->getData() as $x)
					{
						$media = IGMedia::of($account->id)->IGId($x->id)->first();
						if (!$media)
						{
							$media = new IGMedia;
						}

						try {
							$media->account_id			= $account->id;
							$media->ig_id				= $x->id;
							$media->type				= $x->type;
							$media->url					= $x->images->low_resolution->url;
							$media->tags				= implode(' ', $x->tags);
							$media->caption				= $x->caption->text;
							$media->link				= $x->link;
							$media->likes				= $x->likes->count;
							$media->comments			= $x->comments->count;
							$media->posted_at			= \Carbon\Carbon::createFromTimestamp($x->created_time);
							$media->save();

						} catch (Exception $e) {
							
						}
					}

					$max_id = $response->getNextMaxId();

				} while($max_id);
				

				///////////////
				// Completed //
				///////////////
				$bar->advance();
			}
		});

		$bar->finish();
	}
}
