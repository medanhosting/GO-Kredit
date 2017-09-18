<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use Thunderlabid\Survei\Exceptions\AppException;

///////////////
// Framework //
///////////////
use Carbon\Carbon, Auth;

use Thunderlabid\Log\Models\Kredit;
use Thunderlabid\Log\Models\Nasabah;

class SimpanLogKreditViaJaminan
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
	 * @param  SimpanLogKreditViaJaminan $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model 	= $event->data;
		
		$find 	= Kredit::where('pengajuan_id', $model->id)->delete();

		$nasabah_id 	= null;

		if(isset($model->nasabah['nik']))
		{
			$nasabah_id 	= $model->nasabah['nik'];
		}


		switch ($model->jenis) {
			case 'bpkb':
				$jaminan_id 	= $model->dokumen_jaminan['bpkb']['nomor_bpkb'];
				break;
			case 'shm':
				$jaminan_id 	= $model->dokumen_jaminan['shm']['nomor_sertifikat'];
				break;
			case 'shgb':
				$jaminan_id 	= $model->dokumen_jaminan['shgb']['nomor_sertifikat'];
				break;
		}

		$jaminan_tipe 		= $model->jenis;

		$kredit  	= new Kredit;
		$kredit->fill([
			'pengajuan_id'	=> $model->id,
			'nasabah_id'	=> $nasabah_id,
			'jaminan_id'	=> $jaminan_id,
			'jaminan_tipe'	=> $jaminan_tipe,
			]);
		$kredit->save();
	}
}