<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\MutasiJaminan;

use Exception, Carbon\Carbon;

class MutasiJaminanController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$jaminan 	= MutasiJaminan::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));});

		$start 		= Carbon::now()->startofday();
		$end 		= Carbon::now()->endofday();

		if(request()->has('start')){
			$start	= Carbon::createFromFormat('d/m/Y', request()->get('start'))->startofday();
		}
		if(request()->has('end')){
			$end	= Carbon::createFromFormat('d/m/Y', request()->get('end'))->endofday();
		}

		$jaminan 	= $jaminan->where('tanggal', '>=', $start->format('Y-m-d H:i:s'))->where('tanggal', '<=', $end->format('Y-m-d H:i:s'))->orderby('tanggal', 'desc')->paginate();
		view()->share('active_submenu', 'jaminan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.jaminan.index', compact('jaminan'));
		return $this->layout;
	}
}
