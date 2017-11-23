<?php

namespace Thunderlabid\Kredit\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Kredit\Models\Angsuran;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Service\Traits\IDRTrait;

class BebankanBiayaKolektor
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
		$model 					= $event->data;

		//BUTUH PENGECEKAN PUTUSAN
		$angsuran 				= Angsuran::where('nomor_kredit', $model->nomor_kredit)->wherenull('paid_at')->orderby('issued_at', 'asc')->first();

		$a_detail 				= new AngsuranDetail;
		$a_detail->angsuran_id 	= $angsuran['id'];
		$a_detail->ref_id 		= $model->id;
		$a_detail->tag 			= 'collector';
		$a_detail->amount 		= $this->formatMoneyTo(Config::get('kredit.biaya_kolektor'));
		$a_detail->description 	= 'Penagihan tanggal '.$model->collected_at;
		$a_detail->save();
	}
}