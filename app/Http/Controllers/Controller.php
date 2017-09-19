<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Thunderlabid\Manajemen\Models\Kantor;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use Auth;
use Carbon\Carbon;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	function __construct()
	{
		$this->me 		= Auth::user();
		$hari_ini 		= Carbon::now();

		//GET PILIHAN KANTOR
		$penempatan 	= PenempatanKaryawan::where('orang_id', $this->me['id'])->active($hari_ini)->get(['kantor_id'])->toArray();
		$ids 			= array_column($penempatan, 'kantor_id');

		$this->kantor 		= Kantor::WhereIn('id', $ids)->get(['id', 'nama', 'jenis', 'tipe']);
		$this->kantor_aktif	= Kantor::find(request()->get('kantor_aktif_id'));

		//////////////////
		// General Info //
		//////////////////
		view()->share('me', $this->me);
		view()->share('kantor', $this->kantor);
		view()->share('kantor_aktif', $this->kantor_aktif);

		$this->layout 	= view('templates.html.layout');
		$this->layout->html['title'] = 'GO-KREDIT.COM';
	}
}
