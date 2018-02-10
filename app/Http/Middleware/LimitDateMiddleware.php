<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Carbon\Carbon;

class LimitDateMiddleware
{
	public function handle($request, Closure $next, $scopes)
	{
		if(request()->has('tanggal'))
		{
			$scopes = explode('|', $scopes);
			$day 	= explode(' ', request()->get('tanggal'));

			$tanggal_today 	= Carbon::now();
			$tanggal_baru 	= Carbon::createFromFormat('d/m/Y', $day[0]);
			$diff 			= $tanggal_today->diffInDays($tanggal_baru);

			if(in_array('hari_e_0', $scopes) && $diff > 0){
				return redirect()->back()->withErrors('Tidak dapat entry data yang bukan hari ini');
			}
			elseif(in_array('hari_gte_3', $scopes) && $diff > 3){
				return redirect()->back()->withErrors('Tidak dapat entry data lebih dari 3 hari lalu');
			}
			elseif(!in_array('hari_unlimitted', $scopes) && $diff > 0){
				return redirect()->back()->withErrors('Tidak dapat entry data yang bukan hari ini');
			}

			$request->request->add(['tanggal' => $tanggal_baru->format('d/m/Y').' '.$tanggal_today->format('H:i')]);
		}

		return $next($request);
	}
}