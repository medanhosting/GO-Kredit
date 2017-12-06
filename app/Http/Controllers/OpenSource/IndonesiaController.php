<?php

namespace App\Http\Controllers\OpenSource;

use Illuminate\Routing\Controller as BaseController;

////////////////////
// MODEL 		  //
////////////////////
use Thunderlabid\Territorial\Models\Provinsi;

class IndonesiaController extends BaseController
{
	public function index() {
		$indonesia 		= Provinsi::with('regensi')->get();

		return response()->json(['status' => 1, 'data' => $indonesia]);
	}
}

