<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Service\UI\UploadBase64Gambar;
use Exception, Response, Auth;

/**
 * Class UploadGambarController
 * Description: digunakan untuk membantu UI untuk mengambil data
 *
 * author: @agilma <https://github.com/agilma>
 */
Class UploadGambarController extends Controller
{
	public function store()
	{		
		try {
			//1. find latest survei
			if(Auth::user())
			{
				$foto		= base64_decode(request()->get('foto'));
				$fotos		= new UploadBase64Gambar('survei', $foto);
				$fotos		= $fotos->handle();

				return response()->json(['status' => 1, 'data' => $fotos, 'error' => ['message' => []]]);
			}

			return response()->json(['status' => 1, 'data' => [], 'error' => ['message' => []]]);
		} catch (Exception $e) {
			return response()->json(['status' => 1, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
		}
	}

	public function destroy()
	{
		if(Auth::user())
		{
			$filename	= request()->get('foto')['url'];
			$filename 	= str_replace(url('/'), public_path(), $filename);

			if (file_exists($filename) && str_is(public_path().'*', $filename)) 
			{
				unlink($filename);

				return response()->json(['status' => 1, 'data' => [], 'error' => ['message' => []]]);
			}
		} 

		return response()->json(['status' => 1, 'data' => [], 'error' => ['message' => ['foto[url]' => 'Data tidak ada!']]]);
	}
}