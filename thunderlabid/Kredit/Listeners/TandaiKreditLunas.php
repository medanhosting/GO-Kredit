<?php

namespace Thunderlabid\Kredit\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Kredit\Models\Angsuran;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Service\Traits\IDRTrait;
use App\Http\Service\Policy\PerhitunganBunga;

class TandaiKreditLunas
{
	use IDRTrait;
	
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
		$model		= $event->data;

		if(!is_null($model->paid_at)){
			//check tag pelunasan
			$has_paid 	= AngsuranDetail::where('angsuran_id', $model->id)->where('tag', 'pelunasan')->sum('amount');

			$ny_paid 	= Angsuran::where('nomor_kredit', $model->nomor_kredit)->wherenull('paid_at')->get();

			$ids 		= array_column($ny_paid->toArray(), 'id');
			
			$hasnt_paid	= AngsuranDetail::whereIn('angsuran_id', $ids)->where('tag', '<>', 'bunga')->sum('amount');

			if($has_paid >= $hasnt_paid){
				foreach ($ny_paid as $k => $v) {
					$v->delete();
				}
			}
		}
	}
}