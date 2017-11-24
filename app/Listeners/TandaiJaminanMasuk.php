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

		$jaminan 	= MutasiJaminan::where('nomor_kredit', $model->nomor_kredit)->get();

		foreach ($survei['collateral'] as $k => $v) {
			$m_jaminan 					= new MutasiJaminan;
			$m_jaminan->nomor_kredit 	= $model->nomor_kredit;
			$m_jaminan->stored_at 		= Carbon::now()->format('d/m/Y H:i');
			$m_jaminan->documents 		= $v->dokumen_survei['collateral'];
			$m_jaminan->save();
		}
	}
}