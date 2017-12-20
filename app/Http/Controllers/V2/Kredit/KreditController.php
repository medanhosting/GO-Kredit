<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Pengajuan\Models\Pengajuan;

use App\Http\Controllers\V2\Traits\PengajuanTrait;

use Exception;

class KreditController extends Controller
{
	use PengajuanTrait;

	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$aktif 		= Aktif::kantor(request()->get('kantor_aktif_id'))->PembayaranBerikut()->with(['jaminan'])->paginate(15, ['*'], 'aktif');

		view()->share('is_aktif_tab', 'show active');

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.index', compact('aktif'));
		return $this->layout;
	}

	public function show() 
	{
		$aktif 		= Aktif::kantor(request()->get('kantor_aktif_id'))->PembayaranBerikut()->with(['jaminan'])->paginate(15, ['*'], 'aktif');

		view()->share('is_aktif_tab', 'show active');

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.index', compact('aktif'));
		return $this->layout;
	}
}
