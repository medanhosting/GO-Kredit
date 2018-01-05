<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller as BaseController;

use Thunderlabid\Pengajuan\Models\Pengajuan;

use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Survei\Models\SurveiDetail;
use Thunderlabid\Survei\Models\SurveiFoto;

use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use App\Service\Api\ResponseTrait;

use Exception, Response, Auth, Carbon\Carbon;

class SurveiController extends BaseController
{
	use ResponseTrait;

	public function index()
	{
		try {
			//1. find pengajuan
			if(Auth::user()){
				$pkary		= PenempatanKaryawan::where('orang_id', Auth::user()['id'])->active(Carbon::now());

				$survei		= new Survei;

				if(request()->has('kode_kantor')){
					$pkary 		= $pkary->where('kantor_id', request()->get('kode_kantor'));
					$survei 	= $survei->wherehas('pengajuan', function($q){$q->kantor(request()->get('kode_kantor'));});
				}
				
				$survei 	= $survei->wherehas('pengajuan', function($q){$q->status('survei');})->with(['collateral', 'collateral.foto']);
			
				if(request()->has('query')){
					$regexp 	= preg_replace("/-+/",'[^A-Za-z0-9_]+',request()->get('query'));
					$survei 	= $survei->where(function($q)use($regexp){$q->whereHas('collateral', function($q)use($regexp){$q->whereRaw(\DB::raw("dokumen_survei REGEXP '". $regexp."'"));})->orwherehas('pengajuan', function($q)use($regexp){$q->whereraw(\DB::raw("nasabah REGEXP '". $regexp."'"));});});
				}

				$survei 	= $survei->with(['pengajuan'])->paginate();

				$survei->appends(request()->only('kode_kantor', 'query'));
			
				return response()->json(['status' => 1, 'data' => $survei, 'error' => ['message' => []]]);
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
