<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller as BaseController;

use Thunderlabid\Pengajuan\Models\Pengajuan;

use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Survei\Models\SurveiFoto;
use App\Http\Service\UI\UploadBase64Gambar;

use Exception, Response;

class SurveiController extends BaseController
{
	public function upload_foto($pengajuan_id)
	{
		try {
			//1. find latest survei
			if(request()->has('nip_karyawan'))
			{
				$survei		= Survei::where('pengajuan_id', $pengajuan_id)->orderby('tanggal', 'desc')->first();

				$data_foto 	= request()->get('foto');
				$fotos 		= [];

				foreach ($data_foto as $k => $v) {
					//upload
					$survei		= base64_decode($v);
					$fotos[$k]	= new UploadBase64Gambar('survei', $survei);
					$fotos[$k]	= $fotos[$k]->handle();
				}

				$foto				= new SurveiFoto;
				$foto->survei_id 	= $survei['id'];
				$foto->arsip_foto 	= ['foto' => $fotos];
			}

			return Response::json(['status' => 1, 'data' => []]);
		} catch (Exception $e) {
			return Response::json(['status' => 0, 'data' => [], 'pesan' => $e->getMessage()]);
		}
	}

	public function index()
	{
		try {
			//1. find pengajuan
			if(request()->has('nip_karyawan')){
				$pengajuan	= Survei::wherehas('pengajuan', function($q){$q->status('survei');})->where('surveyor->nip', request()->get('nip_karyawan'))->with(['collateral', 'collateral.foto'])->get();
			}
			else{
				throw new Exception("Harus login sebagai karyawan", 1);
			}

			return Response::json(['status' => 1, 'data' => $pengajuan->toArray()]);

		} catch (Exception $e) {
			return Response::json(['status' => 0, 'data' => [], 'pesan' => $e->getMessage()]);
		}
	}
}
