<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller as BaseController;

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

			return Response::json(['Sukses']);
		} catch (Exception $e) {
			return Response::json($e->getMessage());
		}
	}
}
