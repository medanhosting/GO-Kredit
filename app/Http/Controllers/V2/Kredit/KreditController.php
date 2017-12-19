<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Exception;

class KreditController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$aktif 			= [];
		$realisasi 		= [];

		if(request()->has('current')){
			switch (request()->get('current')) {
				case 'realisasi':
					view()->share('is_realisasi_tab', 'show active');
					break;
				default:
					view()->share('is_aktif_tab', 'show active');
					break;
			}			
		}else{
			view()->share('is_aktif_tab', 'show active');
		}

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.index', compact('aktif', 'realisasi'));
		return $this->layout;
	}
}
