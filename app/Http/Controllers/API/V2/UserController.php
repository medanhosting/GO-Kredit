<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller as BaseController;

use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

class UserController extends BaseController
{
	public function show()
	{
		try {
			//1. find pengajuan
			if(Auth::user()){
				$data['me'] 			= Auth::user();
				$data['assignment'] 	= PenempatanKaryawan::where('orang_id', Auth::user()['id'])->active(Carbon::now())->with(['kantor'])->get();
			}
			else{
				$data		= [];
			}

			return response()->json(['status' => 1, 'data' => $data, 'error' => ['message' => []]]);

		} catch (Exception $e) {
			return response()->json(['status' => 0, 'data' => [], 'error' => ['message' => []]]);
		}
	}

}
