<?php

namespace Thunderlabid\Socialmedia\Listeners;

///////////////
// Exception //
///////////////
use Thunderlabid\Socialmedia\Exceptions\AppException;

///////////////
// Framework //
///////////////

class SavingValidation
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
		if (!$model->is_savable) 
		{
			throw new AppException($model->errors, AppException::DATA_VALIDATION);
		}
	}
}