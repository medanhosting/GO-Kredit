<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Carbon\Carbon, Exception;

class LimitDateMiddleware
{
	public function handle($request, Closure $next, $scopes, $scope = '*')
	{
		if(request()->has('tanggal'))
		{
			$scopes = explode('|', $scopes);
			$day 	= explode(' ', request()->get('tanggal'));

			$tanggal_today 	= Carbon::now();
			$tanggal_baru 	= Carbon::createFromFormat('d/m/Y', $day[0]);
			$diff 			= $tanggal_today->diffInDays($tanggal_baru);

			$wewenang_today 	= [$scope.'.hari_e_0', $scope.'.hari_gte_3', $scope.'.hari_unlimitted', '*.hari_e_0', '*.hari_gte_3', '*.hari_unlimitted'];
			$wewenang_3d 		= [$scope.'.hari_gte_3', $scope.'.hari_unlimitted', '*.hari_gte_3', '*.hari_unlimitted'];
			$wewenang_u 		= [$scope.'.hari_unlimitted', '*.hari_unlimitted'];

			//kalau tanggal hari ini
			//1. Jika punya wewenang, lanjut
			if($diff == 0){
				if(!array_intersect($wewenang_today, $scopes)){
					return redirect()->back()->withErrors('Tidak dapat entry data yang bukan hari ini');
				}
			}
			elseif($diff > 0 && $diff <= 3){
				if(!array_intersect($wewenang_3d, $scopes)){
					return redirect()->back()->withErrors('Tidak dapat entry data lebih dari 3 hari lalu');
				}
			}
			elseif($diff > 3){
				if(!array_intersect($wewenang_u, $scopes)){
					return redirect()->back()->withErrors('Tidak dapat entry data yang bukan hari ini');
				}
			}

			$request->request->add(['tanggal' => $tanggal_baru->format('d/m/Y').' '.$tanggal_today->format('H:i')]);
		}

		return $next($request);
	}

	public static function check($scopes, $scope = '*')
	{
		if(request()->has('tanggal'))
		{
			$scopes = explode('|', $scopes);
			$day 	= explode(' ', request()->get('tanggal'));

			$tanggal_today 	= Carbon::now();
			$tanggal_baru 	= Carbon::createFromFormat('d/m/Y', $day[0]);
			$diff 			= $tanggal_today->diffInDays($tanggal_baru);

			$wewenang_today 	= [$scope.'.hari_e_0', $scope.'.hari_gte_3', $scope.'.hari_unlimitted', '*.hari_e_0', '*.hari_gte_3', '*.hari_unlimitted'];
			$wewenang_3d 		= [$scope.'.hari_gte_3', $scope.'.hari_unlimitted', '*.hari_gte_3', '*.hari_unlimitted'];
			$wewenang_u 		= [$scope.'.hari_unlimitted', '*.hari_unlimitted'];

			//kalau tanggal hari ini
			//1. Jika punya wewenang, lanjut
			if($diff == 0){
				if(!array_intersect($wewenang_today, $scopes)){
					throw new Exception("Tidak dapat entry data yang bukan hari ini!", 1);
				}
			}
			elseif($diff > 0 && $diff <= 3){
				if(!array_intersect($wewenang_3d, $scopes)){
					throw new Exception("Tidak dapat entry data lebih dari 3 hari lalu!", 1);
				}
			}
			elseif($diff > 3){
				if(!array_intersect($wewenang_u, $scopes)){
					throw new Exception("Tidak dapat entry data yang bukan hari ini!", 1);
				}
			}

			request()->merge(['tanggal' => $tanggal_baru->format('d/m/Y').' '.$tanggal_today->format('H:i')]);
		}

		return true;
	}
}