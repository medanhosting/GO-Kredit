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
		$active_u	= Auth::user();
		$active_p 	= PenempatanKaryawan::where('kantor_id', $request->get('kantor_aktif_id'))->where('orang_id', $active_u['id'])->active(Carbon::now())->first();

		if(!$active_p)
		{
			throw new Exception("Forbidden", 403);
		}

		if(in_array($scope, $active_p['scopes']))
		{
			return $next($request);
		}

		throw new Exception("Forbidden", 403);
	}
}