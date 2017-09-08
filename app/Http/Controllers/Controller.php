<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Auth;
use Thunderlabid\SocialMedia\Models\Account;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected $social_media_list = ['instagram', 'facebook', 'twitter'];

	function __construct()
	{
		$this->social_media_list = Account::$types;
		asort($this->social_media_list);

		////////////////////
		// Get My Account //
		////////////////////
		$social_media = Account::active()->owner($this->me->id)->orderBy('name')->get();
		view()->share('my_social_media', $social_media);

		//////////////////
		// General Info //
		//////////////////
		$this->me = Auth::user();
		view()->share('me', Auth::user());
		view()->share('social_media_list', array_combine($this->social_media_list, $this->social_media_list));

		$this->layout = view('templates.html.layout');
		$this->layout->html['title'] = 'KLEPON';
	}
}
