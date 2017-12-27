<?php

namespace App\Http\Controllers\V2\Kantor;

use App\Http\Controllers\Controller;

use Thunderlabid\Manajemen\Models\Kantor;

use Exception, DB, Auth, Carbon\Carbon;

class KantorController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$kantor		= new Kantor;

		if(request()->has('q')){
			$kantor = $kantor->where('nama', 'like', '%'.request()->get('q').'%');
		}

		if(request()->has('jenis')){
			switch (strtolower(request()->get('jenis'))) {
				case 'bpr':
				case 'koperasi':
					$kantor 	= $kantor->where('jenis', request()->get('jenis'));
					break;
			}
		}
		
		if(request()->has('tipe')){
			switch (strtolower(request()->get('tipe'))) {
				case 'holding':
				case 'pusat':
				case 'cabang':
					$kantor 	= $kantor->where('tipe', request()->get('tipe'));
					break;
			}
		}

		if(request()->has('sort')){
			switch (strtolower(request()->get('sort'))) {
				case 'nama-asc':
					$kantor 	= $kantor->orderby('nama', 'asc');
					break;
				default:
					$kantor 	= $kantor->orderby('nama', 'desc');
					break;
			}
		}else{
			$kantor = $kantor->orderby('nama', 'asc');
		}

		$kantor 	= $kantor->paginate(15, ['*'], 'kantor');

		view()->share('is_kantor_tab', 'show active');

		view()->share('active_submenu', 'kantor');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kantor.index', compact('kantor'));
		return $this->layout;
	}
}