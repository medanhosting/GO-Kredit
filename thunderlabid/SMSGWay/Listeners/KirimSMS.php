<?php

namespace Thunderlabid\SMSGway\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use GuzzleHttp\Client, Config;

class KirimSMS
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle event
	 * @param  UserCreated $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model = $event->data;
		if (str_is($model->status, 'pending')) 
		{
			$token 		= Config::get('messagebird.access_key');
			$origin 	= Config::get('messagebird.originator');

			//curl mode
			$headers 	= [
				'Authorization' => 'AccessKey ' . $token,
			];
			$body 		= [
				'recipients' 	=> $model->penerima['telepon'],
				'originator' 	=> $origin,
				'body' 			=> $model->isi,
			];

			$client = new Client(); //GuzzleHttp\Client
			$result = $client->request('POST', 'https://rest.messagebird.com/messages', ['headers' => $headers, 'body' => $body]);

			if($result->getStatusCode()==200){
				$array 	= json_decode($result->getBody(), true);

				$model->status 	= 'completed';
				$model->respon 	= $array;
				$model->save();
			}
		}
	}
}