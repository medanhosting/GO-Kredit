<?php

namespace Thunderlabid\Kredit\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Kredit\Models\Angsuran;
use Thunderlabid\Kredit\Models\MutasiJaminan;

use Carbon\Carbon;

class TandaiJaminanKeluar
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

		$not_yet_paid 	= Angsuran::where('nomor_kredit', $model->nomor_kredit)->wherenull('paid_at')->count();
		if(!$not_yet_paid){
			$jaminan 	= MutasiJaminan::where('nomor_kredit', $model->nomor_kredit)->get();
			foreach ($jaminan as $k => $v) {
				$v->taken_at 		= Carbon::now()->format('d/m/Y H:i');
				$v->save();
			}
		}
	}
}