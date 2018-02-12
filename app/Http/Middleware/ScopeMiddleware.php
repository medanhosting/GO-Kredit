<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure, Auth, Exception;
use Carbon\Carbon;

use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

class ScopeMiddleware
{
	public function handle($request, Closure $next, $scope)
	{
		$hari_ini 	= Carbon::now();
		
		$active_u	= Auth::user();
		$active_p 	= PenempatanKaryawan::where('kantor_id', $request->get('kantor_aktif_id'))->where('orang_id', $active_u['id'])->active($hari_ini)->first();

		if(!$active_p)
		{
			return redirect()->back()->withErrors('Anda tidak memiliki wewenang untuk data/proses ini!');
		}

		if(str_is($scope, 'setuju') || str_is($scope, 'tolak'))
		{
			$scope 	= 'keputusan';
		}
		if(in_array($scope, $active_p['scopes']))
		{
			return $next($request);
		}

		$scopes 	= explode('|', $scope);

		foreach ($scopes as $k => $v) {
			if(str_is('*.*', $v)){

				$flag 		= true;
				$v_scope 	= explode('.', $v);
				foreach ($v_scope as $k2 => $v2) {
					if(!in_array($v2, $active_p['scopes']))
					{
						$flag 	= false;
					}
				}

				if($flag){
					return $next($request);
				}
			}elseif(in_array($v, $active_p['scopes'])){
				return $next($request);
			}
		}

		return redirect()->back()->withErrors('Anda tidak memiliki wewenang untuk data/proses ini!');
	}

	public static function check($scope)
	{
		$hari_ini 	= Carbon::now();
		
		$active_u	= Auth::user();
		$active_p 	= PenempatanKaryawan::where('kantor_id', request()->get('kantor_aktif_id'))->where('orang_id', $active_u['id'])->active($hari_ini)->first();

		if(!$active_p)
		{
			throw new Exception("Anda tidak memiliki wewenang untuk data/proses ini!", 1);
		}

		if(str_is($scope, 'setuju') || str_is($scope, 'tolak'))
		{
			$scope 	= 'keputusan';
		}
		if(in_array($scope, $active_p['scopes']))
		{
			return true;
		}

		$scopes 	= explode('|', $scope);
		foreach ($scopes as $k => $v) {
			if(str_is('*.*', $v)){

				$flag 		= true;
				$v_scope 	= explode('.', $v);
				foreach ($v_scope as $k2 => $v2) {
					if(!in_array($v2, $active_p['scopes']))
					{
						$flag 	= false;
					}
				}

				if($flag){
					return true;
				}
			}elseif(in_array($v, $active_p['scopes'])){
				return true;
			}
		}

		throw new Exception("Anda tidak memiliki wewenang untuk data/proses ini!", 1);
	}
}