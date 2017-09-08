<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;

use Thunderlabid\Manajemen\Models\PengaturanScopes;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

class DashboardController extends Controller
{
	public function home() 
	{
		//atur menu scopes
		$hari_ini 				= Carbon::now();
		$scopes 				= $this->getScopesMenu($hari_ini);

		$this->layout->pages 	= view('dashboard.overview', compact('scopes'));
		return $this->layout;
	}


	//ALL THESE PRIVATE FUNCTION TO HELP UI GET THE DATA THEY NEEDED
	private function getScopesMenu($hari_ini)
	{
		$scopes 		= PenempatanKaryawan::where('orang_id', $this->me['id'])->where('kantor_id', $this->kantor_aktif['id'])->active($hari_ini)->first()->toArray();

		$all_scopes 	= PengaturanScopes::wherenull('scope_id')->with(['features', 'features.features', 'features.features.features'])->get()->toArray();

		return $this->recursiveScopes($all_scopes, $scopes);
	}

	private function recursiveScopes($all_scopes, $scopes)
	{
		foreach ($all_scopes as $key => $value) 
		{
			if(in_array($value['scope'], $scopes['scopes']))
			{
				$all_scopes[$key]['enable']	= true;
			}
			else
			{
				$all_scopes[$key]['enable']	= false;
			}
			
			if((array)$value['features'])
			{
				$all_scopes[$key]['features'] 	= $this->recursiveScopes($value['features'], $scopes);
			}
			
			if(!isset($all_scopes[$key+1]))
			{
				return $all_scopes;
			}
		}
	}
}
