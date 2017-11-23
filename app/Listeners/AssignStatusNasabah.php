<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use Thunderlabid\Pengajuan\Exceptions\AppException;

///////////////
// Framework //
///////////////
use Carbon\Carbon, Auth;

use Thunderlabid\Log\Models\Kredit;

class AssignStatusNasabah
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
	 * @param  KantorCreated $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model = $event->data;

		//check data nasabah
		if(isset($model->nasabah['nik']))
		{
			$check_nasabah 	= Pengajuan::status('realisasi')->wherenotnull('pengajuan_id')->get(['pengajuan_id']);
			$ids 			= array_column($check_nasabah, 'pengajuan_id');

			if(count($ids))
			{
				// $surveys 	= Survey::where('pengajuan_id', )
			}
			
		}
	}
}