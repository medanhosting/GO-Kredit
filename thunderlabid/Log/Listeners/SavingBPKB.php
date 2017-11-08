n<?php

namespace Thunderlabid\Log\Listeners;

///////////////
// Exception //
///////////////
use Thunderlabid\Log\Exceptions\AppException;

///////////////
// Framework //
///////////////

class SavingBPKB
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
	 * @param  BPKBCreated $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model = $event->data;
		if (!$model->is_savable) 
		{
			throw new AppException($model->errors, AppException::DATA_VALIDATION);
		}
	}
}