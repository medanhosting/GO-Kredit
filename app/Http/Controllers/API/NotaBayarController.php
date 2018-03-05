<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller as BaseController;

use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use App\Service\Api\ResponseTrait;

use Exception, Response, Auth, Carbon\Carbon;

class NotaBayarController extends BaseController
{
	use ResponseTrait;

	public function index()
	{
		try {
			//1. find pengajuan
			if(Auth::user()){
				$k		= PenempatanKaryawan::where('orang_id', Auth::user()['id'])->active(Carbon::now());
				$nb		= new NotaBayar;

				if(request()->has('kode_kantor')){
					$k	= $k->where('kantor_id', request()->get('kode_kantor'));
				}

				$k 		= $k->first();
				
				// if(request()->has('query')){
				// 	$regexp 	= preg_replace("/-+/",'[^A-Za-z0-9_]+',request()->get('query'));
				// 	$nb 	= $nb->where(function($q)use($regexp){$q->whereHas('collateral', function($q)use($regexp){$q->whereRaw(\DB::raw("dokumen_survei REGEXP '". $regexp."'"));})->orwherehas('pengajuan', function($q)use($regexp){$q->whereraw(\DB::raw("nasabah REGEXP '". $regexp."'"));});});
				// }

				$nb 	= $nb->Where('karyawan->nip', $k['orang']['nip'])->where('jenis', 'kolektor')->paginate();

				$nb->appends(request()->only('kode_kantor', 'query'));
			
				return response()->json(['status' => 1, 'data' => $nb, 'error' => ['message' => []]]);
			}

			return response()->json(['status' => 1, 'data' => [], 'error' => ['message' => []]]);

		} catch (Exception $e) {
			return $this->error_response(request()->all(), $e);
		}
	}

	public function show($id)
	{
		try {
			//1. find pengajuan
			if(Auth::user()){
				$k		= PenempatanKaryawan::where('orang_id', Auth::user()['id'])->active(Carbon::now());
				$nb		= new NotaBayar;

				if(request()->has('kode_kantor')){
					$k	= $k->where('kantor_id', request()->get('kode_kantor'));
				}

				$k 		= $k->first();
				
				// if(request()->has('query')){
				// 	$regexp 	= preg_replace("/-+/",'[^A-Za-z0-9_]+',request()->get('query'));
				// 	$nb 	= $nb->where(function($q)use($regexp){$q->whereHas('collateral', function($q)use($regexp){$q->whereRaw(\DB::raw("dokumen_survei REGEXP '". $regexp."'"));})->orwherehas('pengajuan', function($q)use($regexp){$q->whereraw(\DB::raw("nasabah REGEXP '". $regexp."'"));});});
				// }

				$nb 	= $nb->Where('karyawan->nip', $k['orang']['nip'])->where('jenis', 'kolektor')->where('id', $id)->with(['details'])->first();

				return response()->json(['status' => 1, 'data' => $nb, 'error' => ['message' => []]]);
			}

			return response()->json(['status' => 1, 'data' => [], 'error' => ['message' => []]]);

		} catch (Exception $e) {
			return $this->error_response(request()->all(), $e);
		}
	}
}
