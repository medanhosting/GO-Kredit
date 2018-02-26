<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Carbon\Carbon;

use Thunderlabid\Pengajuan\Models\Putusan;
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
		$model 		= $event->data;

		//BUTUH PENGECEKAN PUTUSAN
		if($model->jenis =='pencairan' && $model->morph_reference_tag =='kredit'){
			$putusan 	= Putusan::where('nomor_kredit', $model->morph_reference_id)->first();

			//SIMPAN KREDIT
			$aktif 		= Aktif::where('nomor_pengajuan', $model->pengajuan_id)->first();

			if(!$aktif){
				$aktif 	= new Aktif;
			}
			$aktif->nomor_kredit 	= $putusan->nomor_kredit;
			$aktif->nomor_pengajuan = $putusan->pengajuan_id;
			$aktif->jenis_pinjaman 	= $putusan->pengajuan->analisa->jenis_pinjaman;
			$aktif->nasabah 		= $putusan->pengajuan->nasabah;
			$aktif->plafon_pinjaman = $putusan->plafon_pinjaman;
			$aktif->suku_bunga 		= $putusan->suku_bunga;
			$aktif->jangka_waktu 	= $putusan->jangka_waktu;
			$aktif->provisi 		= $putusan->perc_provisi;
			$aktif->administrasi 	= $putusan->administrasi;
			$aktif->legal 			= $putusan->legal;
			$aktif->biaya_notaris 	= $putusan->biaya_notaris;
			$aktif->persentasi_denda= $putusan->persentasi_denda;
			$aktif->tanggal 		= $putusan->pengajuan->status_realisasi->tanggal;
			$aktif->kode_kantor 	= $putusan->pengajuan->kode_kantor;
			$aktif->ao 				= $putusan->pengajuan->ao;
			$aktif->save();
		}
	}
}