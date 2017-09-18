<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;

use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

class DashboardController extends Controller
{
	public function home() 
	{
		//atur menu scopes
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
		
		$this->layout->pages 	= view('dashboard.overview');
		return $this->layout;
	}
}
