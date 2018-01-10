<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\AngsuranDetail;
use App\Http\Service\Policy\PelunasanAngsuran;
use App\Service\Traits\IDRTrait;

use Exception, DB, Auth, Carbon\Carbon;

class DendaController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index() 
	{
		$start 		= Carbon::now()->startofday();
		$end 		= Carbon::now()->endofday();

		if(request()->has('start')){
			$start	= Carbon::createFromFormat('d/m/Y', request()->get('start'))->startofday();
		}
		if(request()->has('end')){
			$end	= Carbon::createFromFormat('d/m/Y', request()->get('end'))->endofday();
		}

		$denda 	= AngsuranDetail::where('')->paginate();

		view()->share('active_submenu', 'denda');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.denda.index', compact('denda'));
		return $this->layout;
	}
}
