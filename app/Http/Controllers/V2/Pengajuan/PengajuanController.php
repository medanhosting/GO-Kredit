<?php

namespace App\Http\Controllers\V2\Pengajuan;

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

use Exception, Validator;

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
			$this->status_permohonan($permohonan);

			if (!$permohonan)
			{
				throw new Exception("Data tidak ada!", 1);
			}

			$survei 		= Survei::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->with(['character', 'condition', 'capacity', 'capital', 'jaminan_kendaraan', 'jaminan_tanah_bangunan'])->first();

			$analisa 		= Analisa::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();
			$putusan 		= Putusan::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();
			$r_nasabah 		= $this->riwayat_kredit_nasabah($permohonan['nasabah']['nik'], $id);

			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
			HelperController::variable_list_select();

			if ($permohonan['status_terakhir']['status'] == 'permohonan') {
				view()->share('is_active_permohonan', 'active');
			} elseif ($permohonan['status_terakhir']['status'] == 'survei') {
				view()->share('is_active_survei', 'active');
			} elseif ($permohonan['status_terakhir']['status'] == 'analisa') {
				view()->share('is_active_analisa', 'active');
			} elseif ($permohonan['status_terakhir']['status'] == 'putusan') {
				view()->share('is_active_putusan', 'active');
			}

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

	protected function status_permohonan($permohonan) 
	{
		$checker 	= [];
		$complete 	= 0;
		$total 		= 3;

		//CHECKER KREDIT
		if (is_null($permohonan['pokok_pinjaman'])) {
			$checker['kredit']	= false;
			$complete 			= 0;
		} else {
			$checker['kredit']	= true;
			$complete 			= 3;
		}

		//CHECKER NASABAH
		$rule_n 	= Nasabah::rule_of_valid();
		$total 		= $total + count($rule_n);

		if (count($permohonan['nasabah'])) {
			$validator 	= Validator::make($permohonan['nasabah'], $rule_n);

			if ($validator->fails()) {
				$complete 				= $complete + (count($rule_n) - count($validator->messages()));
				$checker['nasabah'] 	= false;
			} else {
				$complete 				= $complete + count($rule_n);
				$checker['nasabah'] 	= true;
			}
		} else {
			$checker['nasabah'] 		= false;
		}

		//CHECKER KELUARGA
		$rule_k 		= Nasabah::rule_of_valid_family();

		if (count($permohonan['nasabah']['keluarga'])) {
			foreach ($permohonan['nasabah']['keluarga'] as $kk => $kv) {
				$total 		= $total + count($kv);
				$validator 	= Validator::make($kv, $rule_k);

				if ($validator->fails()) {
					$complete 				= $complete + (count($kv) - count($validator->messages()));
					$checker['keluarga'] 	= false;
				} else {
					$complete 				= $complete + count($kv);
					$checker['keluarga'] 	= true;
				}
			}
		} else {
			$total 						= $total + count($rule_k);
			$checker['keluarga'] 		= false;
		}

		//CHECKER JAMINAN
		$flag_jam 			= true;

		if (count($permohonan['jaminan_kendaraan'])) {
			foreach ($permohonan['jaminan_kendaraan'] as $k => $v) {
				$c_col 		= Jaminan::rule_of_valid_jaminan_bpkb();
				$total 		= $total + count($c_col);
				$validator 	= Validator::make($v['dokumen_jaminan'][$v['jenis']], $c_col);

				if ($validator->fails()) {
					$complete 				= $complete + (count($c_col) - count($validator->messages()));
					$checker['jaminan'] 	= false;
					$permohonan['jaminan_kendaraan'][$k]['is_lengkap'] = false;
				} else {
					$complete 				= $complete + count($c_col);

					if (is_null($checker['jaminan']) || $checker['jaminan']) {
						$checker['jaminan'] 	= true;
					}

					$permohonan['jaminan_kendaraan'][$k]['is_lengkap'] = true;
				}

				if (!$v['dokumen_jaminan'][$v['jenis']]['is_lama']) {
					$flag_jam 	= false;
				}
			}
		}

		if (count($permohonan['jaminan_tanah_bangunan'])) {
			foreach ($permohonan['jaminan_tanah_bangunan'] as $k => $v) {
				$c_col 		= Jaminan::rule_of_valid_jaminan_sertifikat($v['jenis'], $v['dokumen_jaminan'][$v['jenis']]['jenis']);
				$total 		= $total + count($c_col);
				$validator 	= Validator::make($v['dokumen_jaminan'][$v['jenis']], $c_col);

				if ($validator->fails()) {
					$complete 				= $complete + (count($c_col) - count($validator->messages()));
					$checker['jaminan'] 	= false;
					$permohonan['jaminan_tanah_bangunan'][$k]['is_lengkap'] = false;
				} else {
					$complete 				= $complete + count($c_col);

					if(is_null($checker['jaminan']) || $checker['jaminan']) {
						$checker['jaminan'] 	= true;
					}
					$permohonan['jaminan_tanah_bangunan'][$k]['is_lengkap'] = true;
				}

				if(!$v['dokumen_jaminan'][$v['jenis']]['is_lama']) {
					$flag_jam 	= false;
				}
			}
		}

		if (!count($permohonan['jaminan_kendaraan']) && !count($permohonan['jaminan_tanah_bangunan'])) {
			$total 		= $total + 1;
		}

		$percentage 	= floor(($complete / max($total, 1)) * 100);

		view()->share('checker', $checker);
		view()->share('percentage', $percentage);
	}
}
