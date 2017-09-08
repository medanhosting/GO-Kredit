<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Thunderlabid\Socialmedia\Models\Account;
use Thunderlabid\Socialmedia\Models\Watchlist;
use Thunderlabid\Socialmedia\Models\Pokelist;
use GuzzleHttp\Client;
use Exception;

class IGAddPokeList extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'klepon:IGAddPokeList';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Push to poke list';
	protected $loop_depth	= 2;

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
		$this->info('IG: Add Poke List');
		$this->info("--------------------------------------------------------");
		$accounts = Account::type('instagram')->chunk(30, function($accounts){
		
			foreach ($accounts as $account)
			{
				if (!$account->access_token)
				{
					continue;
				}

				$this->info(' * ' . $account->type . ': ' . '"'.$account->name.'"' );

				/////////////////////////
				// Create Progress bar //
				/////////////////////////
				$bar = $this->output->createProgressBar(Watchlist::of($account->id)->active()->count());

				/////////////////////////
				// Loop each watchlist //
				/////////////////////////
				Watchlist::of($account->id)->active()->chunk(50, function($watchlists) use ($account, $bar) {

					if (!$account->is_api_limit_reached)
					{
						foreach ($watchlists as $watchlist)
						{
							try {
								switch ($watchlist->type) {
									case 'ig-tag-mediaowner':
										$this->process_ig_tag_mediaowner($account, $watchlist);
										break;
									case 'ig-tag-medialikers':
										// $this->process_ig_tag_medialikers($account, $watchlist);
										break;
									case 'ig-location-mediaowner':
										// $this->process_ig_tag_medialikers($account, $watchlist);
										break;
									case 'ig-account-owner':
										$this->process_ig_account_owner($account, $watchlist);
										break;
									case 'ig-account-followers':
										break;
								}
							} catch (Exception $e) {
								if ($e->getCode() == 429)
								{
									$account->is_api_limit_reached = true;
								}							
							}
						}
					}
				});
			}
		});
	}

	protected function process_ig_tag_mediaowner($account, $watchlist)
	{
		$this->info("     - In Progress: Add media owner of people who post in tag " . $watchlist->value . ': ' . $url);
		////////////////////////////
		// Get Latest Feed by Tag //
		////////////////////////////
		$client = new Client;
		do { 
			$loop++;
			$next_url 	= '';
			
			//////////////
			// API CALL //
			//////////////
			$url 			= 'https://api.instagram.com/v1/tags/' . $watchlist->value . '/media/recent?access_token=' . $account->access_token;
			$response 		= $client->request('GET', $url);
			$result		 	= json_decode((string)$response->getBody());

			if ($result->meta->code == 200)
			{
				//////////////////////////
				// PROCESS CURRENT CALL //
				//////////////////////////
				foreach ($result->data as $media)
				{
					$this->info("          - Processing: " . $media->user->full_name);
					$pokelist = Pokelist::of($account->id)->where('user_id', '=', $media->user->id)->first();

					///////////////////
					// Save pokelist //
					///////////////////
					if (!$pokelist)
					{
						$pokelist = new Pokelist([
										'user_id'	=> $media->user->id,
										'fullname'	=> $media->user->full_name,
										'username'	=> $media->user->username,
										'is_active'	=> true,
							]);
						$pokelist->account()->associate($account);
						try {
							$pokelist->save();
						} catch (Exception $e) {
						
						}
					}

					////////////////////////////////
					// Link pokelist to wathclist //
					////////////////////////////////
					$watchlist_ids = $pokelist->watchlists->map(function($item){ return $item->id; });
					$watchlist_ids->push($watchlist->id);
					$pokelist->watchlists()->sync($watchlist_ids->toArray());
				}
				$next_url = $response->pagination->next_url;
			}
			elseif ($result->meta->code == 429)
			{
				throw new Exception("Rate Limit", 429);
			}
		} while ($next_url || $loop < $this->loop_depth);
	}

	protected function process_ig_tag_medialikers($account, $watchlist)
	{
		$this->info("     - In Progress: Add media owner of people who post in tag " . $watchlist->value . ': ' . $url);
		////////////////////////////
		// Get Latest Feed by Tag //
		////////////////////////////
		$client = new Client;
		do { 
			$loop++;
			$next_url 	= '';
			
			//////////////
			// API CALL //
			//////////////
			$url 			= 'https://api.instagram.com/v1/tags/' . $watchlist->value . '/media/recent?access_token=' . $account->access_token;
			$response 		= $client->request('GET', $url);
			$result		 	= json_decode((string)$response->getBody());

			if ($result->meta->code == 200)
			{
				//////////////////////////
				// PROCESS CURRENT CALL //
				//////////////////////////
				foreach ($result->data as $media)
				{
					$this->info("          - Processing: Media " . $media->id);






					$pokelist = Pokelist::of($account->id)->where('user_id', '=', $media->user->id)->first();

					///////////////////
					// Save pokelist //
					///////////////////
					if (!$pokelist)
					{
						$pokelist = new Pokelist([
										'user_id'	=> $media->user->id,
										'fullname'	=> $media->user->full_name,
										'username'	=> $media->user->username,
										'is_active'	=> true,
							]);
						$pokelist->account()->associate($account);
						try {
							$pokelist->save();
						} catch (Exception $e) {
						
						}
					}

					////////////////////////////////
					// Link pokelist to wathclist //
					////////////////////////////////
					$watchlist_ids = $pokelist->watchlists->map(function($item){ return $item->id; });
					$watchlist_ids->push($watchlist->id);
					$pokelist->watchlists()->sync($watchlist_ids->toArray());
				}
				$next_url = $response->pagination->next_url;
			}
			elseif ($result->meta->code == 429)
			{
				throw new Exception("Rate Limit", 429);
			}
		} while ($next_url || $loop < $this->loop_depth);
	}

	protected function process_ig_account_owner($account, $watchlist)
	{
		////////////////////////////////////////////////////////////////
		// If user hasnt been added to pokelist, get user information //
		////////////////////////////////////////////////////////////////
		if (!$watchlist->pokelists->count())
		{
			$url = 'https://api.instagram.com/v1/users/search?q='.$watchlist->value.'&access_token=' . $account->access_token;
			$response 		= $client->request('GET', $url);
			$result		 	= json_decode((string)$response->getBody());

			if ($result->meta->code == 200)
			{
				foreach ($result->data as $user)
				{
					if (str_is(strtolower($watchlist->value), strtolower($user->username)))
					{
						$watchlist->pokelists()->create([
								'user_id'	=> $user->id,
								'fullname'	=> $user->first_name . ' ' . $user->last_name,
								'username'	=> $user->username,
								'is_active'	=> true,
							]);
					}
				}
			}
			elseif ($result->meta->code == 429)
			{
				throw new Exception("Rate Limit", 429);
			}
		}
	}
}
