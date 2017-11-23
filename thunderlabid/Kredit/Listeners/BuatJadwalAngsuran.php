<?php

namespace Thunderlabid\Kredit\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Kredit\Models\Angsuran;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Http\Service\Policy\PerhitunganBunga;

use Carbon\Carbon;

class BuatJadwalAngsuran
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
		$model 	= $event->data;

		$pb 	= new PerhitunganBunga($model->plafon_angsuran, 'Rp 0', $model->suku_bunga/12, $model->provisi, $model->administrasi, $model->legal);


		foreach ($pb['angsuran'] as $k => $v) {
			$angsuran 					= new Angsuran;
			$angsuran->nomor_kredit 	= $model->nomor_kredit;
			$angsuran->issued_at 		= Carbon::now()->addmonths($k)->format('d/m/Y H:i');
			$angsuran->save();

			$a_detail 	= new AngsuranDetail;
			$a_detail->angsuran_id 	= $angsuran->id;
			$a_detail->tag 			= 'pokok';
			$a_detail->amount 		= $v['angsuran_pokok'];
			$a_detail->save();

			$a_detail 	= new AngsuranDetail;
			$a_detail->angsuran_id 	= $angsuran->id;
			$a_detail->tag 			= 'bunga';
			$a_detail->amount 		= $v['angsuran_bunga'];
			$a_detail->save();
		}
	}
}