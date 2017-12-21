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
		$start 		= Carbon::now()->startOfDay();
		$end 		= Carbon::now()->endOfDay();

		$jaminan 	= MutasiJaminan::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));});

		if(request()->has('q')){
			list($start, $end)	= explode(' - ', request()->get('q'));
			$start 	= Carbon::createFromFormat('d/m/Y', $start);
			$end 	= Carbon::createFromFormat('d/m/Y', $end);
		}

		$jaminan 	= $jaminan->where('tanggal', '>=', $start->format('Y-m-d H:i:s'))->where('tanggal', '<=', $end->format('Y-m-d H:i:s'))->orderby('tanggal', 'desc')->paginate();

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.jaminan.index', compact('jaminan'));
		return $this->layout;
	}
}
