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

use Thunderlabid\Pengajuan\Models\Status;

class SimpanStatusDoingAnalisa
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
	 * @param  KantorCreated $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model = $event->data;
		
		if(Auth::check())
		{
			$karyawan 	= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
		}
		else
		{
			$karyawan 	= $model->analis;
		}

		$mulai_analisa 		= Carbon::createFromFormat('d/m/Y H:i', $model->tanggal)->startofday()->format('Y-m-d H:i:s');
		$selesai_analisa 	= Carbon::createFromFormat('d/m/Y H:i', $model->tanggal)->endofday()->format('Y-m-d H:i:s');

		$status_last 	= Status::where('status', 'analisa')->where('progress', 'sedang')->where('pengajuan_id', $model->pengajuan_id)->where('karyawan->nip', $karyawan['nip'])->orderby('tanggal', 'desc')->first();

		$data 			= [
			'tanggal'		=> $model->tanggal,
			'status'		=> 'analisa',
			'progress'		=> 'sedang',
			'karyawan'		=> $karyawan,
			'pengajuan_id'	=> $model->pengajuan_id,
		];

		if($status_last){
			$status 		= $status_last->update($data);
		}else{
			$status 		= Status::create($data);
		}
	}
}