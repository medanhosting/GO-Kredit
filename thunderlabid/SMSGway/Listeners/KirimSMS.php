<?php

namespace Thunderlabid\SMSGway\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use GuzzleHttp\Client, Config;
use GuzzleHttp\Psr7\Request;

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
			$header 	= 'Authorization: AccessKey ' . $token;

			$body 		= [
				'recipients='.$model->penerima['telepon'],
				'originator='.$origin,
				'body='.$model->isi,
			];

			$body	= implode('&', $body);

			$ch 	= curl_init();

			curl_setopt($ch, CURLOPT_URL, "https://rest.messagebird.com/messages");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
			curl_setopt($ch, CURLOPT_POST, 1);

			$headers 	= array();
			$headers[] = $header;
			$headers[] = "Content-Type: application/x-www-form-urlencoded";
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result 	= json_decode(curl_exec($ch), true);
			if (curl_errno($ch)) {
				throw new AppException(["SMS" => $ch], 1);
			}
			curl_close ($ch);

			// $client = new Client(['headers' => $headers]);
			// $request = $client->post("https://rest.messagebird.com/messages");
			// $request->setPostFields($body);
			// $request->send();

			// $client = new Client(); //GuzzleHttp\Client
			// $client->addHeaders 	
			// $result = $client->request('POST', 'https://rest.messagebird.com/messages', ['headers' => $headers, 'form_params' => $body]);
			if($result['id']){
				$array 	= $result;

				$model->status 	= 'completed';
				$model->respon 	= $array;
				$model->save();
			}
		}
	}
}