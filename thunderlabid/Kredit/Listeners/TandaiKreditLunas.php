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
		$model	= $event->data;

		if(!is_null($model->paid_at)){
			$th 		= new PerhitunganBunga($model->aktif->plafon_angsuran, 'Rp 0', $model->aktif->suku_bunga/12, $model->aktif->provisi, $model->aktif->administrasi, $model->aktif->legal);

			$angsuran 	= Angsuran::where('nomor_kredit', $model->nomor_kredit)->get(['id'])->toArray();

			$ids 		= array_column($angsuran, 'id');

			$sum	 	= AngsuranDetail::whereIn('angsuran_id', $ids)->selectraw('sum(amount) as lunas')->first();

			//re - check cara hitung pinjaman lunas
			if($this->formatMoneyTo($th['total_pinjaman']) <= $sum['lunas']){
				$not_yet_paid 	= Angsuran::where('nomor_kredit', $model->nomor_kredit)->wherenull('paid_at')->get();
				foreach ($not_yet_paid as $k => $v) {
					$v->delete();
				}
			}
		}
	}
}