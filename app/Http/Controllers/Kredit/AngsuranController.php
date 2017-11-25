<?php

namespace App\Http\Controllers\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Angsuran;
use Carbon\Carbon;

class AngsuranController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$today 		= Carbon::now();

		$angsuran 	= Angsuran::countAmount()->where('kode_kantor', request()->get('kantor_aktif_id'))->where('issued_at', '>=', $today->format('Y-m-d H:i:s'))->paginate();

		view()->share('angsuran', $angsuran);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.angsuran.index');
		return $this->layout;
	}

	public function show($id) 
	{
		$today 		= Carbon::now();

		$angsuran 	= Angsuran::countAmount()->where('kode_kantor', request()->get('kantor_aktif_id'))->where('k_angsuran.id', $id)->with(['details'])->first();

		view()->share('angsuran', $angsuran);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.angsuran.show');
		return $this->layout;
	}
}
