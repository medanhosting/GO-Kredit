<?php

namespace Thunderlabid\Kredit\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Kredit\Models\Angsuran;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Service\Traits\IDRTrait;
use Config, Carbon\Carbon;

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
		$model		= $event->data;

		$limit 		= Carbon::createFromFormat('d/m/Y H:i', $model->tanggal)->subDays(Config::get('kredit.batas_pembayaran_angsuran_hari'))->startOfDay();

		//check tunggakan terakhir
		$last 		= AngsuranDetail::wherenull('nota_bayar_id')->where('nomor_kredit', $model->nomor_kredit)->where('tanggal', '<=', $limit->format('Y-m-d H:i:s'))->orderby('nth', 'asc')->first();

		$a_d				= new AngsuranDetail;
		$a_d->nomor_kredit 	= $model['nomor_kredit'];
		$a_d->tanggal 		= $model['tanggal'];
		$a_d->nth 			= $last['nth'];
		$a_d->tag 			= 'collector';
		$a_d->amount 		= $this->formatMoneyTo(Config::get('kredit.biaya_kolektor'));
		$a_d->description 	= 'Biaya penagihan tanggal '.$model->tanggal;
		$a_d->save();
	}
}