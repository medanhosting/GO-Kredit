<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Carbon\Carbon;

use Thunderlabid\Kredit\Models\Aktif;
use App\Service\Traits\KreditGeneratorTrait;

class CadangkanLegalitas
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
		if($event->data->status =='setuju'){
			$model->checklists 		= $this->basic_checklists($model);
			$model->nomor_kredit 	= $this->generateNomorKredit($model);
			$model->save();
		}
	}

	protected function basic_checklists($model)
	{
		$pengajuan 	= $model->pengajuan;

		//required
		$checklists['objek']['fotokopi_ktp_pemohon']	= 'tidak_ada';
		$checklists['objek']['fotokopi_npwp_siup']		= 'tidak_ada';
		$checklists['objek']['foto_jaminan']			= 'tidak_ada';
		$checklists['pengikat']['permohonan_kredit']	= 'tidak_ada';
		$checklists['pengikat']['survei_report']		= 'tidak_ada';
		$checklists['pengikat']['persetujuan_komite']	= 'tidak_ada';
		$checklists['pengikat']['perjanjian_kredit']	= 'tidak_ada';
		$checklists['pengikat']['pengakuan_hutang']		= 'tidak_ada';
		$checklists['pengikat']['pernyataan_analis']	= 'tidak_ada';
		$checklists['pengikat']['kuasa_menjual_dan_menarik_jaminan']	= 'tidak_ada';

		//cadangkan jika tidak ada data keluarga
		$mark 	= 'cadangkan';
		if(count($pengajuan->nasabah['keluarga'])){
			$mark 	= 'tidak_ada';
		}

		$checklists['objek']['fotokopi_kk']						= $mark;
		$checklists['objek']['fotokopi_ktp_keluarga']			= $mark;
		$checklists['pengikat']['surat_persetujuan_keluarga']	= $mark;


		// cadangkan jika tidak ada status perkawinan nikah/cerai
		$mark 	= 'cadangkan';
		if(!str_is($pengajuan->nasabah['status_perkawinan'], 'belum_menikah')){
			$mark 	= 'tidak_ada';
		}
		$checklists['objek']['fotokopi_akta_nikah_cerai_pisah_harta']	= $mark;

		// cadangkan jika bukan jaminan tanah bangunan 
		$mark 	= 'cadangkan';
		if(count($pengajuan->jaminan_tanah_bangunan)){
			$mark 	= 'tidak_ada';
		}

		$checklists['objek']['sertifikat_asli_dan_fotokopi']= $mark;
		$checklists['objek']['ajb']							= $mark;
		$checklists['objek']['imb']							= $mark;
		$checklists['objek']['pbb_terakhir']				= $mark;
		$checklists['pengikat']['skmht_apht']				= $mark;
		$checklists['pengikat']['surat_persetujuan_plang']	= $mark;

		// cadangkan jika bukan jaminan kendaraan 
		$mark 	= 'cadangkan';
		if(count($pengajuan->jaminan_kendaraan)){
			$mark 	= 'tidak_ada';
		}

		$checklists['objek']['check_fisik'] 				= $mark;
		$checklists['objek']['bpkb_asli_dan_fotokopi']		= $mark;
		$checklists['objek']['fotokopi_faktur_dan_stnk']	= $mark;
		$checklists['objek']['kwitansi_jual_beli_kosongan']	= $mark;
		$checklists['objek']['kwitansi_ktp_sesuai_bpkb']	= $mark;
		$checklists['pengikat']['feo']						= $mark;
		$checklists['pengikat']['kuasa_pembebanan_feo']		= $mark;

		// opsional 
		$checklists['objek']['asuransi_kendaraan']				= 'tidak_ada';
		$checklists['pengikat']['penggantian_jaminan']			= 'tidak_ada';
		$checklists['pengikat']['pernyataan_belum_balik_nama']	= 'tidak_ada';

		return $checklists;
	}
}