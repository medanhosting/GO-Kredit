<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Kredit\Models\Jaminan;
use Thunderlabid\Kredit\Models\MutasiJaminan;

use App\Service\System\Calculator;

use Carbon\Carbon, Artisan;

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

		if(in_array($model->notabayar->jenis, ['angsuran', 'angsuran_sementara', 'denda'])){
			$tgl 	= explode(' ', $model->notabayar->tanggal);

			Artisan::call('gokredit:hitungdenda', ['--nomor' => $model->morph_reference_id, '--tanggal' => $tgl[0]]);

			//sisa hutang
			$hutang 	= Calculator::hutangBefore($model->morph_reference_id, Carbon::createfromformat('d/m/Y', $tgl[0])->adddays(1));
			$denda 		= Calculator::dendaBefore($model->morph_reference_id, Carbon::createfromformat('d/m/Y', $tgl[0])->adddays(1));

			if(!$hutang && !$denda){
				$jaminan 	= Jaminan::where('nomor_kredit', $model->morph_reference_id)->get();
				foreach ($jaminan as $k => $v) {
					$m_jaminan 					= new MutasiJaminan;
					$m_jaminan->nomor_jaminan 	= $v->nomor_jaminan;
					$m_jaminan->tanggal 		= $model->notabayar->tanggal;
					$m_jaminan->status 			= 'titipan';
					$m_jaminan->progress 		= 'valid';
					$m_jaminan->tag 			= $v->status_terakhir->tag;
					$m_jaminan->deskripsi 		= 'Angsuran Lunas';
					$m_jaminan->karyawan 		= $v->status_terakhir->karyawan;
					$m_jaminan->save();
				}
			}
		}
	}
}