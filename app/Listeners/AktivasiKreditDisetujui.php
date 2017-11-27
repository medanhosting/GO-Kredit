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
			$aktif 		= Aktif::where('nomor_pengajuan', $model->pengajuan_id)->first();

			if(!$aktif){
				$aktif 	= new Aktif;
			}
			$aktif->nomor_kredit 	= $this->generateNomorKredit($model);
			$aktif->nomor_pengajuan = $model->pengajuan_id;
			$aktif->jenis_pinjaman 	= $model->pengajuan->analisa->jenis_pinjaman;
			$aktif->nasabah 		= $model->pengajuan->nasabah;
			$aktif->plafon_pinjaman = $model->plafon_pinjaman;
			$aktif->suku_bunga 		= $model->suku_bunga;
			$aktif->jangka_waktu 	= $model->jangka_waktu;
			$aktif->provisi 		= $model->perc_provisi;
			$aktif->administrasi 	= $model->administrasi;
			$aktif->legal 			= $model->legal;
			$aktif->tanggal 		= $model->tanggal;
			$aktif->kode_kantor 	= $model->pengajuan->kode_kantor;
			$aktif->save();
		}
	}

	protected function generateNomorKredit($model)
	{
		$first_letter       = 'K.'.Carbon::now()->format('ym').'.';
		$prev_data          = Aktif::where('nomor_kredit', 'like', $first_letter.'%')->orderby('nomor_kredit', 'desc')->first();

		if($prev_data)
		{
			$last_letter	= explode('.', $prev_data['nomor_kredit']);
			$last_letter	= ((int)$last_letter[2] * 1) + 1;
		}
		else
		{
			$last_letter	= 1;
		}

		$last_letter		= str_pad($last_letter, 4, '0', STR_PAD_LEFT);

		return $first_letter.$last_letter;
	}
}