<?php

namespace App\Http\Controllers\V2\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Pengajuan\Models\Analisa;
use Thunderlabid\Pengajuan\Models\Putusan;

use Thunderlabid\Log\Models\Kredit;

use App\Http\Controllers\V2\Traits\PengajuanTrait;

use Exception;

class PengajuanController extends Controller
{
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
					view()->share('is_survei_tab', 'show active');
					break;
				case 'analisa':
					view()->share('is_analisa_tab', 'show active');
					break;
				case 'putusan':
					view()->share('is_putusan_tab', 'show active');
					break;
				default:
					view()->share('is_permohonan_tab', 'show active');
					break;
			}			
		}else{
			view()->share('is_permohonan_tab', 'show active');
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

			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

			$this->layout->pages 	= view('v2.pengajuan.show', compact('permohonan', 'survei', 'analisa', 'putusan', 'r_nasabah'));
			return $this->layout;

		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
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
