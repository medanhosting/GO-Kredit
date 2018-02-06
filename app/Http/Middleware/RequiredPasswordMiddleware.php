<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure, Auth, Hash, Exception;
use Carbon\Carbon;

use App\Exceptions\AppException;

class RequiredPasswordMiddleware
{
	public function handle($request, Closure $next)
	{
		$active_u	= Auth::user();
		
		if(!Hash::check(request()->get('password'), $active_u['password']))
		{
			return redirect()->back()->withErrors('Password tidak cocok!');
		}

		return $next($request);
	}

	public function check()
	{
		$active_u	= Auth::user();

		if(!Hash::check(request()->get('password'), $active_u['password']))
		{
			throw new Exception("Password tidak cocok!", 1);
		}

		return true;
	}
}