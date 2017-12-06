<?php

namespace Thunderlabid\Kredit\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Kredit\Models\AngsuranDetail;
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

		$not_yet_paid 	= AngsuranDetail::where('tanggal', '>', Carbon::createFromFormat('d/m/Y H:i', $model->tanggal))->wherenull('nota_bayar_id')->where('tag', '<>', 'bunga')->count();

		if(!$not_yet_paid){
			$jaminan 	= MutasiJaminan::where('nomor_kredit', $model->nomor_kredit)->get();
			foreach ($jaminan as $k => $v) {
				$m_jaminan 					= new MutasiJaminan;
				$m_jaminan->nomor_kredit 	= $v->nomor_kredit;
				$m_jaminan->tanggal 		= Carbon::now()->format('d/m/Y H:i');
				$m_jaminan->tag 			= 'out';
				$m_jaminan->description 	= 'Jaminan Keluar';
				$m_jaminan->documents 		= $v->documents;
				$m_jaminan->save();
			}
		}
	}
}