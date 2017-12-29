<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller as BaseController;

use Thunderlabid\Pengajuan\Models\Pengajuan;

use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Survei\Models\SurveiDetail;
use Thunderlabid\Survei\Models\SurveiFoto;

use App\Service\Api\ResponseTrait;

use Exception, Response, Auth;

class SurveiController extends BaseController
{
	use ResponseTrait;

	public function index()
	{
		try {
			//1. find pengajuan
			if(Auth::user()){
				$survei		= Survei::wherehas('pengajuan', function($q){$q->status('survei');})->with(['collateral', 'collateral.foto'])->paginate();
			
				return response()->json(['status' => 1, 'data' => $survei->toArray(), 'error' => ['message' => []]]);
			}

			return response()->json(['status' => 1, 'data' => [], 'error' => ['message' => []]]);

		} catch (Exception $e) {
			return $this->error_response(request()->all(), $e);
		}
	}

	public function simpan_foto($pengajuan_id, $survei_detail_id)
	{
		try {
			//1. find latest survei
			if(Auth::user())
			{
				$survei			= Survei::where('pengajuan_id', $pengajuan_id)->orderby('tanggal', 'desc')->first();
				$survei_detail 	= SurveiDetail::where('survei_id', $survei['id'])->where('id', $survei_detail_id)->firstorfail();

				$fotos 		= request()->get('foto');

				$s_foto 	= SurveiFoto::where('survei_detail_id', $survei_detail_id)->first();

				if(!$s_foto)
				{
					$s_foto = new SurveiFoto;
				}

				$s_foto->survei_detail_id 	= $survei_detail_id;
				$s_foto->arsip_foto 		= $fotos;
				$s_foto->save();

				return response()->json(['status' => 1, 'data' => $survei->toArray(), 'error' => ['message' => []]]);
			}

			return response()->json(['status' => 1, 'data' => [], 'error' => ['message' => []]]);
		} catch (Exception $e) {
			return $this->error_response(request()->all(), $e);
		}
	}
}
