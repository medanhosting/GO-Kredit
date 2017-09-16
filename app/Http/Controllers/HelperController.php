<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Input;

use Thunderlabid\Territorial\Models\Regensi;
use Thunderlabid\Territorial\Models\Distrik;
use Thunderlabid\Territorial\Models\Desa;

/**
 * Class HelperController
 * Description: digunakan untuk membantu UI untuk mengambil data
 *
 * author: @agilma <https://github.com/agilma>
 */
Class HelperController extends Controller
{
	/**
	 * fungsi get cities
	 * Description: berfungsi untuk mendapatkan city dari id province tertentu
	 */
	public function getRegensi()
	{
		$id 			= request()->input('id');
		// get data regensi
		$regensi		= collect(Regensi::where('territorial_provinsi_id', $id)->get());
		// sort data city by 'nama'
		$regensi 		= $regensi->sortBy('nama');
		// $regensi 		= $regensi->pluck('nama', 'id');

        return response()->json($regensi->pluck('nama', 'id'));
	}

	/**
	 * fungsi get distrik
	 * Description: untuk mendapatkan distrik dari regensi tertentu
	 */
	public function getDistrik()
	{
		$id 			= request()->input('id');
		// get data distrik for regensi 'id'
		$distrik 		= collect(Distrik::where('territorial_regensi_id', $id)->get());
		// sort data distrik by 'nama'
		$distrik 		= $distrik->sortBy('nama');
		$distrik 		= $distrik->pluck('nama', 'id');

		return response()->json($distrik);
	}

	/**
	 * fungsi get desa
	 * Description: untuk mendapatkan desa dari distrik tertentu
	 */
	public function getDesa()
	{
		$id 			= request()->input('id');
		// get data desa dari distrik 'nama';
		$desa 		= collect(Desa::where('territorial_distrik_id', $id)->get());// sort data desa by 'nama'
		$desa 		= $desa->sortBy('nama');
		$desa 		= $desa->pluck('nama', 'id');

		return response()->json($desa);
	}

	public function storeGambar(Request $request)
	{		
		$input 		= $request->input('_file');

		$survei 	= base64_decode($input);
		$gambar 	= new UploadBase64Gambar('survei', ['image' => $survei]);
		$gambar 	= $gambar->handle();

		return JSend::success($gambar)->asArray();
	}

	public function destroyGambar()
	{
		$filename	= Input::get('url');
		$filename 	= str_replace(url('/'), public_path(), $filename);

		if (file_exists($filename) && str_is(public_path().'*', $filename)) 
		{
			unlink($filename);

			return JSend::success([])->asArray();
		} 

		return JSend::error(['File tidak ada!'])->asArray();
	}
}