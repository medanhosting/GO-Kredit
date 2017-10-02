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
		return $next($request);
		$hari_ini 	= Carbon::now();
		
		$active_u	= Auth::user();
		$active_p 	= PenempatanKaryawan::where('kantor_id', $request->get('kantor_aktif_id'))->where('orang_id', $active_u['id'])->active($hari_ini)->first();

		if(!$active_p)
		{
			return redirect()->back()->withErrors('Forbidden!');
		}

		if(str_is($scope, 'setuju') || str_is($scope, 'tolak'))
		{
			$scope 	= 'keputusan';
		}
		if(in_array($scope, $active_p['scopes']))
		{
			return $next($request);
		}

		return redirect()->back()->withErrors('Forbidden!');
		throw new Exception("Forbidden", 403);
	}
}