<?php

namespace App\Http\Controllers;

use Auth;

class DashboardController extends Controller
{
	public function overview() 
	{
		$this->layout->pages = view('dashboard.overview');
		return $this->layout;
	}
}
