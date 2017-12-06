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
		$indonesia 	= Provinsi::with('regensi')->get();

		$id 		= [];
		foreach ($indonesia as $k => $v) {
			foreach ($v['regensi'] as $k2 => $v2) {
				$id[$v['nama']][]	= $v2['nama'];
			}
		}

		return response()->json(['status' => 1, 'data' => $id, 'error' => ['message' => []]]);
	}
}

