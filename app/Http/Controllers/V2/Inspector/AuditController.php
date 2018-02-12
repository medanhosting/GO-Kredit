<?php

namespace App\Http\Controllers\V2\Inspector;

use App\Http\Controllers\Controller;

use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Survei\Models\SurveiPasscode;

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
		view()->share('active_submenu', 'audit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.inspector.audit.index');
		return $this->layout;
	}
}