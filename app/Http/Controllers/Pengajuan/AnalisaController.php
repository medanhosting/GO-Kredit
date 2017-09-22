<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Analisa;

use Exception, Auth;
use Carbon\Carbon;

class AnalisaController extends Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->middleware('scope:analisa');
	}

	public function store($id = null)
	{
		try {
			$analisa 			= Analisa::where('pengajuan_id', $id)->wherehas('pengajuan', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->orderby('tanggal', 'desc')->first();

			if(!$analisa)
			{
				$analisa 		= new Analisa;
			}

			$data_input 				= request()->all();
			$data_input['analis']		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
			$data_input['pengajuan_id']	= $id;

			$analisa->fill($data_input);
			$analisa->save();

			return redirect(route('pengajuan.pengajuan.show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id'), 'status' => 'analisa']));
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}

	}

	public function update($id)
	{
		return $this->store($id);
	}
}
