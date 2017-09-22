<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Survei\Models\SurveiDetail;

use Exception, Auth;
use Carbon\Carbon;

class SurveiController extends Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->middleware('scope:survei');
	}

	public function assign($id = null)
	{
		try {
			$permohonan		= Pengajuan::where('id', $id)->status('permohonan')->kantor(request()->get('kantor_aktif_id'))->first();

			if(!$permohonan)
			{
				throw new Exception("Data tidak ditemukan", 1);
			}

			$survei 				= new Survei;
			$survei->tanggal 		= Carbon::now()->format('d/m/Y H:i');
			$survei->surveyor		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
			$survei->pengajuan_id 	= $id;
			$survei->save();

			return redirect(route('pengajuan.pengajuan.show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id'), 'status' => 'survei']));
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}

 	}

	public function store($id = null)
	{
		try {
			$survei 			= Survei::where('pengajuan_id', $id)->wherehas('pengajuan', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->with(['character', 'condition', 'capacity', 'capital', 'collateral'])->orderby('tanggal', 'desc')->first();

			if(!$survei)
			{
				throw new Exception("Dokumen ini tidak diijinkan untuk survei", 1);
			}

			if(request()->has('character'))
			{
				$survei->character->delete();

				$character 			= new SurveiDetail;
				$character->fill(request()->get('character'));
				$character->survei_id 	= $survei['id'];
				$character->save();
			}
	
			if(request()->has('condition'))
			{
				$survei->condition->delete();

				$condition 			= new SurveiDetail;
				$condition->fill(request()->get('condition'));
				$condition->survei_id 	= $survei['id'];
				$condition->save();
			}

			if(request()->has('collateral'))
			{
				$survei->collateral->delete();

				$collateral 			= new SurveiDetail;
				$collateral->fill(request()->get('collateral'));
				$collateral->survei_id 	= $survei['id'];
				$collateral->save();
			}

			if(request()->has('capacity'))
			{
				$survei->capacity->delete();

				$capacity 			= new SurveiDetail;
				$capacity->fill(request()->get('capacity'));
				$capacity->survei_id 	= $survei['id'];
				$capacity->save();
			}

			if(request()->has('capital'))
			{
				$survei->capital->delete();

				$capital 			= new SurveiDetail;
				$capital->fill(request()->get('capital'));
				$capital->survei_id 	= $survei['id'];
				$capital->save();
			}
			return redirect(route('pengajuan.pengajuan.show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id'), 'status' => 'survei']));
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}

	}

	public function update($id)
	{
		return $this->store($id);
	}
}
