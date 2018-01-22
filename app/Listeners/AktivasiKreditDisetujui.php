<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Carbon\Carbon;

use Thunderlabid\Kredit\Models\Aktif;
use App\Service\Traits\KreditGeneratorTrait;

class AktivasiKreditDisetujui
{
	use KreditGeneratorTrait;
	
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
	 * @param  MODEL PUTUSAN $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model 		= $event->data->pengajuan->putusan;

		//BUTUH PENGECEKAN PUTUSAN
		if($event->data->status =='realisasi' && $event->data->progress=='sudah' && $model->putusan == 'setuju'){

			//SIMPAN KREDIT
			$aktif 		= Aktif::where('nomor_pengajuan', $model->pengajuan_id)->first();

			if(!$aktif){
				$aktif 	= new Aktif;
			}
			$aktif->nomor_kredit 	= $model->nomor_kredit;
			$aktif->nomor_pengajuan = $model->pengajuan_id;
			$aktif->jenis_pinjaman 	= $model->pengajuan->analisa->jenis_pinjaman;
			$aktif->nasabah 		= $model->pengajuan->nasabah;
			$aktif->plafon_pinjaman = $model->plafon_pinjaman;
			$aktif->suku_bunga 		= $model->suku_bunga;
			$aktif->jangka_waktu 	= $model->jangka_waktu;
			$aktif->provisi 		= $model->perc_provisi;
			$aktif->administrasi 	= $model->administrasi;
			$aktif->legal 			= $model->legal;
			$aktif->biaya_notaris 	= $model->biaya_notaris;
			$aktif->tanggal 		= $model->tanggal;
			$aktif->kode_kantor 	= $model->pengajuan->kode_kantor;
			$aktif->ao 				= $model->pengajuan->ao;
			$aktif->save();
		}
	}
}