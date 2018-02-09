<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Kredit\Models\Jaminan;
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
		$pengajuan 	= Pengajuan::where('id', $model->nomor_pengajuan)->with(['status_realisasi'])->first();

		foreach ($survei['collateral'] as $k => $v) {
			if(in_array($v->dokumen_survei['collateral']['jenis'], ['shgb', 'shm'])){
				$ktgr 	= $v->dokumen_survei['collateral'][$v->dokumen_survei['collateral']['jenis']]['tipe'];
			}else{
				$ktgr 	= $v->dokumen_survei['collateral'][$v->dokumen_survei['collateral']['jenis']]['jenis'];
			}

			$jaminan 					= new Jaminan;
			$jaminan->nomor_kredit 	= $model->nomor_kredit;
			$jaminan->nomor_jaminan = $jaminan->nomor_kredit.'-'.($k+1);
			$jaminan->kategori 		= $ktgr;
			$jaminan->dokumen 		= $v->dokumen_survei['collateral'];
			$jaminan->save();

			$mj				= new MutasiJaminan;
			$mj->nomor_jaminan = $jaminan->nomor_jaminan;
			$mj->tanggal 		= $model->tanggal;
			$mj->tag 			= 'in';
			$mj->status 		= 'aktif';
			$mj->progress 		= 'menunggu_validasi';
			$mj->deskripsi 		= 'Jaminan Masuk';
			$mj->karyawan 		= $pengajuan['status_realisasi']['karyawan'];
			$mj->save();
		}
	}
}