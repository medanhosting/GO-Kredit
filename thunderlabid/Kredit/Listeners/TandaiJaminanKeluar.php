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

		if(!is_null($model->nota_bayar_id)){
			$not_yet_paid 	= AngsuranDetail::wherenull('nota_bayar_id')->where('nomor_kredit', $model->nomor_kredit)->count();

			if(!$not_yet_paid){
				$ids 		= MutasiJaminan::where('nomor_kredit', $model['nomor_kredit'])->selectraw('max(id) as id, max(tanggal) as tanggal')->groupby('nomor_jaminan')->orderby('tanggal', 'desc')->get()->toArray();

				$jaminan 	= MutasiJaminan::wherein('id', array_column($ids, 'id'))->get();
				foreach ($jaminan as $k => $v) {
					$m_jaminan 					= new MutasiJaminan;
					$m_jaminan->nomor_kredit 	= $v->nomor_kredit;
					$m_jaminan->nomor_jaminan 	= $v->nomor_jaminan;
					$m_jaminan->kategori 		= $v->kategori;
					$m_jaminan->tanggal 		= Carbon::now()->format('d/m/Y H:i');
					$m_jaminan->tag 			= $v->tag;
					$m_jaminan->status 			= 'titipan';
					$m_jaminan->deskripsi 		= 'Angsuran Lunas';
					$m_jaminan->dokumen 		= $v->dokumen;
					$m_jaminan->karyawan 		= $v->karyawan;
					$m_jaminan->save();
				}
			}
		}
	}
}