<?php

namespace Thunderlabid\Finance\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Carbon\Carbon;

class NoPaymentAfter3PM
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

		$now 	= Carbon::now();

		if((($now->format('G') * 1) > 14 || ($now->format('G') * 1) < 8) && !str_is($model->jenis, 'kolektor')){
			throw new AppException(['tanggal' => 'Tidak bisa memproses antara jam 3 sore hingga jam 8 pagi'], AppException::DATA_VALIDATION);
		}
		elseif((($now->format('G') * 1) > 15 || ($now->format('G') * 1) < 8) && str_is($model->jenis, 'kolektor')){
			throw new AppException(['tanggal' => 'Tidak bisa memproses antara jam 4 sore hingga jam 8 pagi'], AppException::DATA_VALIDATION);
		}
	}
}