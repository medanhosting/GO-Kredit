<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Kredit\Models\Jaminan;
use Thunderlabid\Kredit\Models\MutasiJaminan;
use Thunderlabid\Kredit\Models\JadwalAngsuran;

use Thunderlabid\Finance\Models\Jurnal;
use Thunderlabid\Finance\Models\Finance;

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
			$sisa		= Jurnal::where('morph_reference_id', $model->morph_reference_id)->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['120.100', '120.200', '120.300', '120.400', '140.100', '140.200']);})->sum('jumlah');

			if(!$sisa && $sisa){
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