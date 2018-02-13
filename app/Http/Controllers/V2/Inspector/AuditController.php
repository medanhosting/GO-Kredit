<?php

namespace App\Http\Controllers\V2\Inspector;

use App\Http\Controllers\Controller;

use Thunderlabid\Manajemen\Models\Audit;

use Exception, Auth, Carbon\Carbon;

class AuditController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware('scope:'.implode('|', $this->acl_menu['inspektor.audit']));
	}

	public function index () 
	{
		$audit 		= Audit::orderby('tanggal', 'desc');

		if(request()->has('q')){
			$tanggal 	= Carbon::createfromformat('d/m/Y', request()->get('q'));
			$audit 		= $audit->where('tanggal', '>=', $tanggal->startofday()->format('Y-m-d H:i:s'))->where('tanggal', '<=', $tanggal->endofday()->format('Y-m-d H:i:s'));
		}

		$audit 		= $audit->where('kode_kantor', request()->get('kantor_aktif_id'))->paginate();

		view()->share('active_submenu', 'audit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.inspector.audit.index', compact('audit'));
		return $this->layout;
	}
}