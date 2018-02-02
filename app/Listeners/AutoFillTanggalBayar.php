<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Finance\Models\NotaBayar;

use Carbon\Carbon;

class AutoFillTanggalBayar
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
	 * @param  Aktif $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model 		= $event->data;

		if($model->getDirty()['nomor_faktur']){
			$nb 	= NotaBayar::where('nomor_faktur', $model->nomor_faktur)->where('morph_reference_id', $model->nomor_kredit)->where('morph_reference_tag', 'kredit')->first();
			$model->tanggal_bayar 	= $nb->tanggal;
		}
	}
}