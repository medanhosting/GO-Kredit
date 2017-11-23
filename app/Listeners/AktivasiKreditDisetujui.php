<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Carbon\Carbon;

use Thunderlabid\Kredit\Models\Aktif;

class AktivasiKreditDisetujui
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
	 * @param  MODEL PUTUSAN $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model 		= $event->data;

		//BUTUH PENGECEKAN PUTUSAN
		if($model->putusan == 'setuju'){
			$aktif 		= new Aktif;
			$aktif->nomor_kredit 	= $this->generate_no_kredit($model);
			$aktif->nomor_pengajuan = $model->id;
			$aktif->nasabah 		= $model->nasabah;
			$aktif->plafon_pinjaman = $model->plafon_pinjaman;
			$aktif->suku_bunga 		= $model->suku_bunga;
			$aktif->jangka_waktu 	= $model->jangka_waktu;
			$aktif->provisi 		= $model->perc_provisi;
			$aktif->administrasi 	= $model->administrasi;
			$aktif->legal 			= $model->legal;
			$aktif->save();
		}
	}
}