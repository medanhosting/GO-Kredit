<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller as BaseController;

use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use Auth, Carbon\Carbon, Exception;

class UserController extends BaseController
{
	public function show()
	{
		try {
			//1. find pengajuan
			if(Auth::user()){
				$data['me']	= Auth::user();
				$employ 	= PenempatanKaryawan::select(['kantor_id', 'role', 'scopes'])->where('orang_id', Auth::user()['id'])->active(Carbon::now())->with(['kantor' => function($q){$q->select(['nama', 'id']);}])->get();
				$kantor 	= [];

				foreach ($employ as $k => $v) {
					$kantor[$k]['id']		= $v['kantor']['id'];
					$kantor[$k]['nama']	= $v['kantor']['nama'];
					$kantor[$k]['role']	= $v['role'];
					$kantor[$k]['scopes']	= $v['scopes'];
				}

				$data['employment']	= $kantor;
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
