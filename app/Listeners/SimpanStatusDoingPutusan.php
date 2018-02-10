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

class SimpanStatusDoingPutusan
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
			$karyawan 	= $model->pembuat_keputusan;
		}

		$mulai_putusan 		= Carbon::createFromFormat('d/m/Y H:i', $model->tanggal)->startofday()->format('Y-m-d H:i:s');
		$selesai_putusan 	= Carbon::createFromFormat('d/m/Y H:i', $model->tanggal)->endofday()->format('Y-m-d H:i:s');

		$status_last 	= Status::where('status', 'putusan')->where('progress', 'sedang')->where('pengajuan_id', $model->pengajuan_id)->where('karyawan->nip', $karyawan['nip'])->orderby('tanggal', 'desc')->first();

		$data 			= [
			'tanggal'		=> $model->tanggal,
			'status'		=> 'putusan',
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