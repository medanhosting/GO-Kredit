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
		$this->layout->pages 	= view('dashboard.overview');
		return $this->layout;
	}
}
