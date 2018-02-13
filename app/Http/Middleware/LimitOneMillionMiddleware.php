<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Carbon\Carbon, Exception;

use App\Service\Traits\IDRTrait;

class LimitOneMillionMiddleware
{
	use IDRTrait;

	public function handle($request, Closure $next, $scopes, $scope = '*')
	{
		if(request()->has('jumlah'))
		{
			$scopes = explode('|', $scopes);

			$jumlah = $this->formatMoneyFrom(request()->get('jumlah'));

			$wewenang_min 	= [$scope.'.nominatif_lte_1000000', '*.nominatif_lte_1000000', $scope.'.nominatif_unlimitted', '*.nominatif_unlimitted'];
			$wewenang_max 	= [$scope.'.nominatif_gt_1000000', '*.nominatif_gt_1000000', $scope.'.nominatif_unlimitted', '*.nominatif_unlimitted'];

			//kalau jumlah hari ini
			//1. Jika punya wewenang, lanjut
			if($jumlah <= 1000000){
				if(!array_intersect($wewenang_min, $scopes)){
					return redirect()->back()->withErrors('Tidak memiliki wewenang untuk kredit kurang dari sama dengan '.request()->get('jumlah'));
				}
			}
			else{
				if(!array_intersect($wewenang_max, $scopes)){
					return redirect()->back()->withErrors('Tidak memiliki wewenang untuk kredit lebih dari '.request()->get('jumlah'));
				}
			}
		}

		return $next($request);
	}

	public static function check($scopes, $scope = '*')
	{
		if(request()->has('jumlah'))
		{
			$scopes = explode('|', $scopes);

			$jumlah = self::formatMoneyFrom(request()->get('jumlah'));

			$wewenang_min 	= [$scope.'.nominatif_lte_1000000', '*.nominatif_lte_1000000', $scope.'.nominatif_unlimitted', '*.nominatif_unlimitted'];
			$wewenang_max 	= [$scope.'.nominatif_gt_1000000', '*.nominatif_gt_1000000', $scope.'.nominatif_unlimitted', '*.nominatif_unlimitted'];

			//kalau jumlah hari ini
			//1. Jika punya wewenang, lanjut
			if($jumlah <= 1000000){
				if(!array_intersect($wewenang_min, $scopes)){
					throw new Exception('Tidak memiliki wewenang untuk kredit kurang dari sama dengan '.request()->get('jumlah'), 1);
				}
			}
			else{
				if(!array_intersect($wewenang_max, $scopes)){
					throw new Exception('Tidak memiliki wewenang untuk kredit lebih dari '.request()->get('jumlah'), 1);
				}
			}
		}

		return true;
	}
}