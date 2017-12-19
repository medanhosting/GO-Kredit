<?php

namespace App\Http\Controllers\V2\Pengajuan;

use App\Http\Controllers\Controller;
use App\Http\Service\Policy\PerhitunganBunga;

use Exception;

class SimulasiController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index() 
	{
		$mode 		= request()->get('mode');

		$rincian 	= [];

		if(request()->has('pokok_pinjaman'))
		{
			if($mode=='pa')
			{
				$rincian 	= new PerhitunganBunga(request()->get('pokok_pinjaman'), request()->get('kemampuan_angsur'), request()->get('bunga_per_tahun')/12);
				$rincian 	= $rincian->pa();
				view()->share('is_angsuran_tab', 'show active');
			}
			elseif($mode=='pt')
			{
				$rincian 	= new PerhitunganBunga(request()->get('pokok_pinjaman'), request()->get('kemampuan_angsur'), request()->get('bunga_per_tahun')/6);
				$rincian 	= $rincian->pt();
				view()->share('is_musiman_tab', 'show active');
			}
		}else{
			view()->share('is_angsuran_tab', 'show active');
		}

		view()->share('active_submenu', 'simulasi');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.simulasi.index', compact('mode', 'rincian'));
		return $this->layout;
	}
}
