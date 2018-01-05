<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Kredit\Models\MutasiJaminan;

use Carbon\Carbon;

class TandaiJaminanMasuk
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
		$survei 	= Survei::where('pengajuan_id', $model->nomor_pengajuan)->with(['collateral'])->first();

		foreach ($survei['collateral'] as $k => $v) {
			$m_jaminan 					= new MutasiJaminan;
			$m_jaminan->nomor_kredit 	= $model->nomor_kredit;
			$m_jaminan->tanggal 		= $model->tanggal;
			$m_jaminan->tag 			= 'in';
			$m_jaminan->description 	= 'Jaminan Masuk';
			$m_jaminan->nomor_jaminan 	= $m_jaminan->nomor_kredit.'-'.($k+1);
			$m_jaminan->status 			= 'completed';
			$m_jaminan->documents 		= $v->dokumen_survei['collateral'];
			$m_jaminan->save();
		}
	}
}