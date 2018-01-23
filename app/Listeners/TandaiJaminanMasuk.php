<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Pengajuan\Models\Pengajuan;
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

			$m_jaminan 					= new MutasiJaminan;
			$m_jaminan->nomor_kredit 	= $model->nomor_kredit;
			$m_jaminan->nomor_jaminan 	= $m_jaminan->nomor_kredit.'-'.($k+1);
			$m_jaminan->tanggal 		= $model->tanggal;
			$m_jaminan->tag 			= 'in';
			$m_jaminan->kategori 		= $ktgr;
			$m_jaminan->status 			= 'aktif';
			$m_jaminan->deskripsi 		= 'Jaminan Masuk';
			$m_jaminan->dokumen 		= $v->dokumen_survei['collateral'];
			$m_jaminan->karyawan 		= $pengajuan['status_realisasi']['karyawan'];
			$m_jaminan->save();
		}
	}
}