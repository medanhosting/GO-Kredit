<?php

namespace Thunderlabid\Kredit\Listeners;

use Thunderlabid\Kredit\Models\Rekening;

class AutoFillRekeningID
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

		if(is_null($model->rekening_id)){
			$rekening 				= Rekening::first();
			$model->rekening_id 	= $rekening->id;
		}
	}
}