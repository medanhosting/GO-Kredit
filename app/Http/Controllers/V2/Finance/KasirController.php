<?php

namespace App\Http\Controllers\V2\Finance;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Exception, DB, Auth, Carbon\Carbon;

class KasirController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->middleware('scope:kasir');
	}

	public function index () 
	{
		view()->share('active_submenu', 'kasir');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kasir.index');
		return $this->layout;
	}
}