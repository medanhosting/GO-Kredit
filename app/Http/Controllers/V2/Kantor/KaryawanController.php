<?php

namespace App\Http\Controllers\V2\Kantor;

use App\Http\Controllers\Controller;

use Thunderlabid\Manajemen\Models\Orang;
use Thunderlabid\Manajemen\Models\Kantor;

use Exception, DB, Auth, Carbon\Carbon;

class KaryawanController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$kantor 		= Kantor::get(['id', 'nama']);
		$karyawan		= new Orang;

		if(request()->has('q')){
			$karyawan 	= $karyawan->where('nama', 'like', '%'.request()->get('q').'%');
		}

		if(request()->has('kantor') && !str_is(request()->get('kantor'), 'semua')){
			$karyawan 	= $karyawan->wherehas('penempatanaktif', function($q){$q->where('kantor_id', request()->get('kantor'));});
		}

		if(request()->has('sort')){
			switch (strtolower(request()->get('sort'))) {
				case 'nama-asc':
					$karyawan 	= $karyawan->orderby('nama', 'asc');
					break;
				default:
					$karyawan 	= $karyawan->orderby('nama', 'desc');
					break;
			}
		}else{
			$karyawan 	= $karyawan->orderby('nama', 'asc');
		}

		$karyawan 		= $karyawan->with(['penempatanaktif', 'penempatanaktif.kantor'])->paginate(15, ['*'], 'karyawan');

		view()->share('is_karyawan_tab', 'show active');

		view()->share('active_submenu', 'karyawan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kantor.karyawan.index', compact('karyawan', 'kantor'));
		return $this->layout;
	}
}