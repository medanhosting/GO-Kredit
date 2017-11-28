<?php

namespace App\Http\Controllers\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\MutasiJaminan;

use Carbon\Carbon, Exception, DB, Config, Auth;

use App\Service\Traits\IDRTrait;

class JaminanController extends Controller
{
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();

		// $this->middleware('required_password')->only('store');
	}

	public function index () 
	{
		$jaminan 	= MutasiJaminan::where('kode_kantor', request()->get('kantor_aktif_id'));
		if(request()->has('q')){
			$look 		= '%'.request()->get('q').'%';
			$jaminan 	= $jaminan->where(function($q)use($look){
				$q->where('nomor_kredit', 'like', $look)->orwhere('documents', 'like', $look);
			});
		}

		$jaminan 	= $jaminan->orderby('updated_at', 'desc')->paginate();

		view()->share('jaminan', $jaminan);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.jaminan.index');
		return $this->layout;
	}
}
