<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Survei\Models\SurveiDetail;
use Thunderlabid\Survei\Models\SurveiLokasi;

use Exception, Auth, Validator;
use Carbon\Carbon;

class SurveiController extends Controller
{
	protected $view_dir = 'pengajuan.survei.';

	public function __construct()
	{
		parent::__construct();

		$this->middleware('scope:survei');
	}

	public function index ($status = 'survei') 
	{
		$kecamatan 	= SurveiLokasi::whereHas('survei', function($q){
							$q->where('kode_kantor', request()->get('kantor_aktif_id'));
						})->whereHas('survei.surveyor', function($q){
							$q->where('nip', Auth::user()['nip']);
						})->wherenotnull('kota')->wherenotnull('kecamatan')
						->select('kota', 'kecamatan')->selectraw('count(id) as total')
						->orderby('kota', 'asc')->groupby('kota', 'kecamatan')->get();

		$survei 	= SurveiLokasi::whereHas('survei', function($q){
							$q->where('kode_kantor', request()->get('kantor_aktif_id'));
						})->whereHas('survei.surveyor', function($q){
							$q->where('nip', Auth::user()['nip']);
						})->wherenotnull('kota')->wherenotnull('kecamatan');

		if(request()->has('kota'))
		{
			$survei = $survei->where('kota', 'like', request()->get('kota'));
		}

		if(request()->has('kecamatan'))
		{
			$survei = $survei->where('kecamatan', 'like', request()->get('kecamatan'));
		}

		if(count($kecamatan) > 23)
		{
			$take 	= 15;
		}
		else
		{
			$take 	= 12;
		}

		$survei 	= $survei->orderby('created_at', 'desc')->paginate($take);

		view()->share('survei', $survei);
		view()->share('status', $status);
		view()->share('order', $order);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view($this->view_dir . 'index', compact('kecamatan'));
		return $this->layout;
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

	public function show($id, $status = 'survei')
	{
		try {
			$lokasi 	= SurveiLokasi::whereHas('survei', function($q){
					$q->where('kode_kantor', request()->get('kantor_aktif_id'));
				})->whereHas('survei.surveyor', function($q){
					$q->where('nip', Auth::user()['nip']);
				})->where('id', $id)->first();

			$survei 		= Survei::where('id', $lokasi['survei_id'])->orderby('tanggal', 'desc')->with(['character', 'condition', 'capacity', 'capital', 'jaminan_kendaraan', 'jaminan_tanah_bangunan', 'surveyor'])->first();

			$breadcrumb 	= [
				[
					'title'	=> $status,
					'route' => route('pengajuan.survei.index', ['status' => $status, 'kantor_aktif_id' => request()->get('kantor_aktif_id')])
				], 
				[
					'title'	=> 'Kredit '.$survei['pengajuan_id'],
					'route' => route('pengajuan.survei.index', ['status' => $status, 'id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')])
				]
			];


			$checker 	= [];
			$complete 	= 0;
			$total 		= 0;

			//checker character
			$c_char 	= SurveiDetail::rule_of_valid_character();
			$total 		= $total + count($c_char);

			if(count($survei['character']))
			{
				$validator 	= Validator::make($survei['character']['dokumen_survei']['character'], $c_char);
				if ($validator->fails())
				{
					$complete 				= $complete + (count($c_char) - count($validator->messages()));
					$checker['character'] 	= false;
				}
				else
				{
					$complete 				= $complete + count($c_char);
					$checker['character'] 	= true;
				}
			}
			else
			{
				$checker['character'] 		= false;
			}

			//checker condition
			$c_cond 	= SurveiDetail::rule_of_valid_condition();
			$total 		= $total + count($c_cond);

			if(count($survei['condition']))
			{
				$validator 	= Validator::make($survei['condition']['dokumen_survei']['condition'], $c_cond);
				if ($validator->fails())
				{
					$complete 				= $complete + (count($c_cond) - count($validator->messages()));
					$checker['condition'] 	= false;
				}
				else
				{
					$complete 				= $complete + count($c_cond);
					$checker['condition'] 	= true;
				}
			}
			else
			{
				$checker['condition'] 		= false;
			}

			//checker capital
			$c_capi 	= SurveiDetail::rule_of_valid_capital();
			$total 		= $total + count($c_capi);

			if(count($survei['capital']))
			{
				$validator 	= Validator::make($survei['capital']['dokumen_survei']['capital'], $c_capi);
				if ($validator->fails())
				{
					$complete 				= $complete + (count($c_capi) - count($validator->messages()));
					$checker['capital'] 	= false;
				}
				else
				{
					$complete 				= $complete + count($c_capi);
					$checker['capital'] 	= true;
				}
			}
			else
			{
				$checker['capital'] 		= false;
			}

			//checker capacity
			$c_capa 	= SurveiDetail::rule_of_valid_capacity();
			$total 		= $total + count($c_capa);

			if(count($survei['capacity']))
			{
				$validator 	= Validator::make($survei['capacity']['dokumen_survei']['capacity'], $c_capa);
				if ($validator->fails())
				{
					$complete 				= $complete + (count($c_capa) - count($validator->messages()));
					$checker['capacity'] 	= false;
				}
				else
				{
					$complete 				= $complete + count($c_capa);
					$checker['capacity'] 	= true;
				}
			}
			else
			{
				$checker['capacity'] 		= false;
			}

			//checker collateral
			if(count($survei['jaminan_kendaraan'])){
				foreach ($survei['jaminan_kendaraan'] as $k => $v) {
					$c_col 		= SurveiDetail::rule_of_valid_collateral_bpkb();
					$total 		= $total + count($c_col);

					$validator 	= Validator::make($v['dokumen_survei']['collateral']['bpkb'], $c_col);
					if ($validator->fails())
					{
						$complete 				= $complete + (count($c_col) - count($validator->messages()));
						$checker['collateral'] 	= false;
					}
					else
					{
						$complete 				= $complete + count($c_col);
						$checker['collateral'] 	= true;
					}
				}
			}

			if(count($survei['jaminan_tanah_bangunan'])){
				foreach ($survei['jaminan_tanah_bangunan'] as $k => $v) {
					$c_col 		= SurveiDetail::rule_of_valid_collateral_sertifikat($v['dokumen_survei']['collateral']['jenis'], $v['dokumen_survei']['collateral'][$v['dokumen_survei']['collateral']['jenis']]['tipe']);
					$total 		= $total + count($c_col);

					$validator 	= Validator::make($v['dokumen_survei']['collateral'][$v['dokumen_survei']['collateral']['jenis']], $c_col);
					if ($validator->fails())
					{
						$complete 				= $complete + (count($c_col) - count($validator->messages()));
						$checker['collateral'] 	= false;
					}
					else
					{
						$complete 				= $complete + count($c_col);
						$checker['collateral'] 	= true;
					}
				}
			}
			
			$percentage 	= floor(($complete / max($total, 1)) * 100);
			
			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
			view()->share('breadcrumb', $breadcrumb);
			view()->share('status', $status);

			$this->layout->pages 	= view('pengajuan.survei.show', compact('survei', 'lokasi', 'checker', 'percentage'));
			return $this->layout;

		} catch (Exception $e) {
			return redirect(route('pengajuan.survei.index', ['status' => $status, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
		}
	}
}
