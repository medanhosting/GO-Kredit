<?php

namespace App\Http\Middleware;

use Closure;

use Thunderlabid\Manajemen\Models\Kantor;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use Auth, Validator;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;

class ModifyScopesMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null)
	{
		$me 		= Auth::user();
		$hari_ini	= Carbon::now();

		//GET PILIHAN KANTOR
		$kantor 		= Kantor::wherehas('penempatan', function($q)use($hari_ini){$q->where('orang_id', $me['id'])->active($hari_ini);})->orderby('nama', 'asc')->get(['id', 'nama', 'jenis', 'tipe']);

		$kantor_aktif	= Kantor::where('id', request()->get('kantor_aktif_id'))->with(['pimpinan', 'pimpinan.orang'])->first();

		//validating kantor aktif
		if($kantor_aktif && !str_is($kantor_aktif['tipe'], 'holding')){
			$returned 		= $this->check_kantor_aktif($kantor_aktif->toArray());

			if($returned instanceOf MessageBag){
				abort(503, $returned);
			}
		}

		$scopes		= PenempatanKaryawan::where('orang_id', $me['id'])->active($hari_ini)->where('kantor_id', request()->get('kantor_aktif_id'))->first();
		$is_holder 	= false;

		if(in_array('holding', array_column($kantor->toArray(), 'tipe'))){
			$is_holder 	= true;
		}

		$request->request->add(['auth_kantor_aktif' => $kantor_aktif, 'auth_kantor' => $kantor, 'auth_scopes' => $scopes, 'auth_is_holder' => $is_holder, 'auth_me' => $me]);
dd(123);
dd($request()->request()->all());
		return $next($request);
	}

	protected function check_kantor_aktif($kantor){
		$rules['nama']	= ['required'];
		$rules['geolocation.latitude']	= ['required'];
		$rules['geolocation.longitude']	= ['required'];
		$rules['telepon']				= ['required'];
		$rules['alamat.alamat']	= ['required'];
		$rules['alamat.kota']	= ['required'];
		$rules['tipe']			= ['required'];
		$rules['pimpinan.orang.nama']			= ['required'];
		$rules['pimpinan.orang.telepon']		= ['required'];
		$rules['pimpinan.orang.alamat.alamat']	= ['required'];
		$rules['pimpinan.orang.alamat.kota']	= ['required'];

		$messages['nama.required']				= 'Nama Kantor';
		$messages['geolocation.latitude.required'] 		= 'Alamat Kantor';
		$messages['geolocation.longitude.required'] 	= 'Alamat Kantor';
		$messages['alamat.alamat.required'] 	= 'Alamat Kantor';
		$messages['alamat.kota.required'] 		= 'Alamat Kantor';
		
		$messages['tipe.required'] 							= 'Tipe Kantor';
		$messages['telepon.required'] 						= 'Telepon Kantor';
		$messages['pimpinan.orang.nama.required'] 			= 'Pimpinan Kantor';
		$messages['pimpinan.orang.telepon.required'] 		= 'Telepon Pimpinan Kantor';
		$messages['pimpinan.orang.alamat.alamat.required'] 	= 'Alamat Pimpinan Kantor';
		$messages['pimpinan.orang.alamat.kota.required'] 	= 'Alamat Pimpinan Kantor';

		//////////////
		// Validate //
		//////////////
		$validator = Validator::make($kantor, $rules, $messages);
		if ($validator->fails())
		{
			return $validator->messages();
		}
		else
		{
			return 'safe';
		}
	}
}
