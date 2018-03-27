<?php

namespace App\Listeners;

use Carbon\Carbon, Auth;

use Thunderlabid\Manajemen\Models\Audit;

class Auditor
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
	public function handle($event, $model)
	{
		$class 		= explode(' ', $event);

		$object 	= explode("\\", strtolower($class[1]));

		if(isset($object[3]) && !str_is('audit', $object[3]) && !str_is('cetaknotabayar', $object[3]) && Auth::check()){
			$audit 	= new Audit;
			$audit->kode_kantor	= request()->get('kantor_aktif_id');
			$audit->tanggal 	= Carbon::now()->format('d/m/Y H:i');
			$audit->domain 		= $object[1];
			$audit->data_lama 	= $model[0]->getOriginal();
			$audit->data_perubahan 		= $model[0]->getDirty();
			$audit->data_baru 	= $model[0]->getAttributes();
			$audit->karyawan 	= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
			$audit->save();
		}
	}
}