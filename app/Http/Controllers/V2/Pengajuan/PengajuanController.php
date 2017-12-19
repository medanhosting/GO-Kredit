<?php

namespace App\Http\Controllers\V2\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Pengajuan\Models\Analisa;
use Thunderlabid\Pengajuan\Models\Putusan;

use Exception;

class PengajuanController extends Controller
{
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

			$survei 		= Survei::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->with(['character', 'condition', 'capacity', 'capital', 'jaminan_kendaraan', 'jaminan_tanah_bangunan'])->get()->toArray();

			$analisa 		= Analisa::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();
			$putusan 		= Putusan::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();

			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

			$this->layout->pages 	= view('v2.pengajuan.show', compact('permohonan', 'survei', 'analisa', 'putusan'));
			return $this->layout;

		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	private function get_pengajuan($status){
		$result		= Pengajuan::status($status)->kantor(request()->get('kantor_aktif_id'))->with(['status_terakhir', 'jaminan_kendaraan', 'jaminan_tanah_bangunan', 'status_permohonan']);
		
		if (request()->has('q_'.$status))
		{
			$cari	= request()->get('q_'.$status);
			$regexp = preg_replace("/-+/",'[^A-Za-z0-9_]+',$cari);
			$result	= $result->where(function($q)use($regexp)
			{				
				$q
				->whereRaw(\DB::raw('nasabah REGEXP "'.$regexp.'"'));
			});
		}

		if (request()->has('jaminan_'.$status))
		{
			$cari	= request()->get('jaminan_'.$status);
			switch (strtolower($cari)) {
				case 'jaminan-bpkb':
					$result 	= $result->wherehas('jaminan_kendaraan', function($q){$q;});
					break;
				case 'jaminan-sertifikat':
					$result 	= $result->wherehas('jaminan_tanah_bangunan', function($q){$q;});
					break;
			}
		}

		if (request()->has('sort_'.$status)){
			$sort	= request()->get('sort_'.$status);
			switch (strtolower($sort)) {
				case 'nama-desc':
					$result 	= $result->orderby('p_pengajuan.nasabah->nama', 'desc');
					break;
				case 'tanggal-asc':
					$result 	= $result->selectraw('max(p_status.tanggal) as tanggal')->orderby('tanggal', 'asc');
					break;
				case 'tanggal-desc':
					$result 	= $result->selectraw('max(p_status.tanggal) as tanggal')->orderby('tanggal', 'desc');
					break;
				default :
					$result 	= $result->orderby('p_pengajuan.nasabah->nama', 'asc');
					break;
			}
		}else{
			$result 	= $result->orderby('p_pengajuan.nasabah->nama', 'asc');
		}

		$result		= $result->paginate(15, ['*'], $status);

		return $result;
	}
}
