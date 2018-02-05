<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Carbon\Carbon;

class LimitDateMiddleware
{
	public function handle($request, Closure $next)
	{
		if(request()->has('tanggal'))
		{
			$day 	= explode(' ', request()->get('tanggal'));

			$tanggal_today 	= Carbon::now();
			$tanggal_baru 	= Carbon::createFromFormat('d/m/Y', $day[0]);
			$diff 			= $tanggal_today->diffInDays($tanggal_baru);

			if($diff > 0){
				return redirect()->back()->withErrors('Tidak dapat entry data yang bukan hari ini');
			}
		}

		return $next($request);
	}
}