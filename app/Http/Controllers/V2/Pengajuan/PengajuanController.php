<?php

namespace App\Http\Controllers\V2\Pengajuan;

use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Analisa;
use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Pengajuan\Models\Jaminan;

use Thunderlabid\Survei\Models\Survei;

use Thunderlabid\Log\Models\Kredit;
use Thunderlabid\Log\Models\Nasabah;

use App\Http\Controllers\V2\Traits\PengajuanTrait;

use App\Service\Traits\IDRTrait;

use Exception, Validator;

class PengajuanController extends Controller
{
	use IDRTrait;
	use PengajuanTrait;

	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$permohonan 	= $this->get_pengajuan('permohonan');
		$survei 		= $this->get_pengajuan('survei');
		$analisa 		= $this->get_pengajuan('analisa');
		$putusan 		= $this->get_pengajuan('putusan');

		if(request()->has('current')){
			switch (request()->get('current')) {
				case 'survei':
					view()->share('is_survei_tab', 'active show');
					break;
				case 'analisa':
					view()->share('is_analisa_tab', 'active show');
					break;
				case 'putusan':
					view()->share('is_putusan_tab', 'active show');
					break;
				default:
					view()->share('is_permohonan_tab', 'active show');
					break;
			}			
		}else{
			view()->share('is_permohonan_tab', 'active show');
		}

		view()->share('active_submenu', 'pengajuan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.pengajuan.index', compact('permohonan', 'survei', 'analisa', 'putusan'));
		return $this->layout;
	}

	public function show($id){
		try {
			$permohonan		= Pengajuan::where('p_pengajuan.id', $id)->kantor(request()->get('kantor_aktif_id'))->with('jaminan_kendaraan', 'jaminan_tanah_bangunan', 'riwayat_status', 'status_terakhir')->first();
			

			if (!$permohonan)
			{
				throw new Exception("Data tidak ada!", 1);
			}

			$survei 		= Survei::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->with(['character', 'condition', 'capacity', 'capital', 'jaminan_kendaraan', 'jaminan_tanah_bangunan'])->first();

			$analisa 		= Analisa::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();
			$putusan 		= Putusan::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();
			$r_nasabah 		= $this->riwayat_kredit_nasabah($permohonan['nasabah']['nik'], $id);

			view()->share('active_submenu', 'pengajuan');
			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
			HelperController::variable_list_select();

			if ($permohonan['status_terakhir']['status'] == 'permohonan') {
				view()->share('is_active_permohonan', 'active');
				$this->checker_permohonan($permohonan);
			} elseif ($permohonan['status_terakhir']['status'] == 'survei') {
				view()->share('is_active_survei', 'active');
				$this->checker_survei($survei);
			} elseif ($permohonan['status_terakhir']['status'] == 'analisa') {
				view()->share('is_active_analisa', 'active');
				$this->checker_analisa($analisa);
			} elseif ($permohonan['status_terakhir']['status'] == 'putusan') {
				view()->share('is_active_putusan', 'active');
				$this->checker_putusan($putusan);
			}

			$this->layout->pages 	= view('v2.pengajuan.show', compact('permohonan', 'survei', 'analisa', 'putusan', 'r_nasabah'));
			return $this->layout;

		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function store($id = null){
		$permohonan 	= Pengajuan::findornew($id);

		if(str_is($permohonan->status_terakhir->status, 'putusan')){
			$returned 	= $this->store_putusan($permohonan);
		}elseif(str_is($permohonan->status_terakhir->status, 'analisa')){
			$returned 	= $this->store_analisa($permohonan);
		}elseif(str_is($permohonan->status_terakhir->status, 'survei')){
			$returned 	= $this->store_survei($permohonan);
		}elseif(str_is($permohonan->status_terakhir->status, 'permohonan') || is_null($id)){
			$returned 	= $this->store_permohonan($permohonan);
		}

		if($returned instanceof Pengajuan){
			return redirect()->route('pengajuan.show', ['id' => $returned['id'], 'kantor_aktif_id' => request()->get('kantor_aktif_id')]);
		}
		
		return redirect()->back()->withErrors($returned);
	}

	public function create(){
		$this->layout->pages 	= view('v2.pengajuan.create');
		return $this->layout;
	}

	public function assign($id = null){
		$permohonan 	= Pengajuan::findorfail($id);

		$returned 		= [];
		if(str_is($permohonan->status_terakhir->status, 'permohonan')){
			$returned 	= $this->assign_surveyor($permohonan);
		}elseif(str_is($permohonan->status_terakhir->status, 'survei')){
			$returned 	= $this->assign_analis($permohonan);
		}elseif(str_is($permohonan->status_terakhir->status, 'analisa')){
			$returned 	= $this->assign_komite_putusan($permohonan);
		}elseif(str_is($permohonan->status_terakhir->status, 'putusan')){
			$returned 	= $this->assign_realisasi($permohonan);
		}

		if($returned instanceof Model){
			return redirect()->route('pengajuan.show', ['id' => $returned['id'], 'kantor_aktif_id' => request()->get('kantor_aktif_id')]);
		}
		
		return redirect()->back()->withErrors($returned);
	}


	public function update($id){
		return $this->store($id);
	}

	protected function riwayat_kredit_nasabah($nik, $id)
	{
		if(is_null($nik))
		{
			return [];
		}
		
		$k_ids	= array_column(Kredit::where('nasabah_id', $nik)->where('pengajuan_id', '<>', $id)->get()->toArray(), 'pengajuan_id');

		return Pengajuan::wherein('id', $k_ids)->get();
	}
}
