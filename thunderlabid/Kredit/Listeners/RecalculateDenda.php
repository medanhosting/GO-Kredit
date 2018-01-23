<?php

namespace Thunderlabid\Kredit\Listeners;

use Artisan;

class RecalculateDenda
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
	 * @param  PenagihanCreated $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model	= $event->data;

		if(!is_null($model->nota_bayar_id)){
			//recalculate denda
			Artisan::call('gokredit:hitungdenda', ['--nomor' => $model->nomor_kredit]);
			// Artisan::call('gokredit:terbitkansp', ['--nomor' => $model->nomor_kredit]);
		}
	}
}