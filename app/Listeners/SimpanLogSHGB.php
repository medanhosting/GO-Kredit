<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use Thunderlabid\Survei\Exceptions\AppException;

///////////////
// Framework //
///////////////
use Carbon\Carbon, Auth;

use Thunderlabid\Log\Models\SHGB;

class SimpanLogSHGB
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
	 * @param  SimpanLogSHGB $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model 		= $event->data;
		
		if(str_is($model->jenis, 'shgb'))
		{
			$find 	= SHGB::where('nomor_sertifikat', $model->dokumen_jaminan['shgb']['nomor_sertifikat'])->first();

			if(!$find)
			{
				$SHGB 					= new SHGB;
				$data_n_n				= $model->dokumen_jaminan['shgb'];
				unset($data_n_n['tahun_perolehan']);
				$data_n_n['parent_id']	= null;
				$SHGB->fill($data_n_n);
				$SHGB->save();
			}
			else
			{
				//simpan versioning
				$old_SHGB 				= new SHGB;
				$data_o_n 				= $find->toArray();
				unset($data_o_n['tahun_perolehan']);
				$data_o_n['parent_id']	= $find->id;

				$data_n_n				= $model->dokumen_jaminan['shgb'];
				$find->fill($data_n_n);
				$find->save();
			}
		}
	}
}