<?php

namespace App\Http\Controllers\V2\Pengajuan;

use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Analisa;
use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Pengajuan\Models\Jaminan;

use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use Thunderlabid\Survei\Models\Survei;

use Thunderlabid\Log\Models\Kredit;
use Thunderlabid\Log\Models\Nasabah;

use App\Http\Controllers\V2\Traits\PengajuanTrait;

use App\Service\Traits\IDRTrait;

use Exception, Validator, Carbon\Carbon;

use App\Http\Middleware\ScopeMiddleware;
use App\Http\Middleware\LimitDateMiddleware;
use App\Http\Middleware\LimitAmountMiddleware;
use App\Http\Middleware\RequiredPasswordMiddleware;

class PengajuanController extends Controller
{
	use IDRTrait;
	use PengajuanTrait;

	public function __construct()
	{
		parent::__construct();

		$this->middleware('scope:'.implode('|', $this->acl_menu['pengajuan.pengajuan']))->only(['index', 'show']);
		$this->middleware('scope:'.implode('|', $this->acl_menu['pengajuan.permohonan']))->only(['create']);
	}

	public function __call($name, $arguments)
	{
		if(str_is('middleware_*', $name)){
			if(in_array($name, ['middleware_store_permohonan'])){
				ScopeMiddleware::check('permohonan');
				LimitDateMiddleware::check(implode('|', $this->scopes['scopes']));
			}
			if(in_array($name, ['middleware_assign_surveyor'])){
				ScopeMiddleware::check('assign');
				LimitDateMiddleware::check(implode('|', $this->scopes['scopes']));
			}
			if(in_array($name, ['middleware_store_survei', 'middleware_assign_analis'])){
				ScopeMiddleware::check('survei');
				LimitDateMiddleware::check(implode('|', $this->scopes['scopes']), 'survei');
			}
			if(in_array($name, ['middleware_store_analisa', 'middleware_assign_komite_putusan'])){
				ScopeMiddleware::check('analisa');
				LimitDateMiddleware::check(implode('|', $this->scopes['scopes']), 'analisa');
			}
			if(in_array($name, ['middleware_store_putusan', 'middleware_assign_realisasi'])){
				ScopeMiddleware::check('putusan');
				LimitDateMiddleware::check(implode('|', $this->scopes['scopes']));
				LimitAmountMiddleware::check(implode('|', $this->scopes['scopes']), 'putusan');
			}

			if(str_is('middleware_checker*', $name)){
				$status 	= explode('_', $name);
				ScopeMiddleware::check($status[2].'|operasional');
			}

			if(str_is('middleware_assign*', $name)){
				RequiredPasswordMiddleware::check();
			}

			if(str_is('middleware_get_pengajuan', $name)){
				if(!array_intersect([$arguments[0], 'operasional'], $this->scopes['scopes'])){
					$arguments[1]	= null;
					$arguments[2]	= true;

					return call_user_func_array([$this, str_replace('middleware_', '', $name)], $arguments);
				}
				
				if(in_array($arguments[0], ['survei', 'analisa']) && !in_array('operasional', $this->scopes['scopes'])){
					request()->merge(['karyawan_'.$arguments[0] => $this->me['nip']]);
				}
			}

			return call_user_func_array([$this, str_replace('middleware_', '', $name)], $arguments);				
		}
	}

	public function index () 
	{
		$permohonan 	= $this->middleware_get_pengajuan('permohonan');
		$survei 		= $this->middleware_get_pengajuan('survei');
		$analisa 		= $this->middleware_get_pengajuan('analisa');
		$putusan 		= $this->middleware_get_pengajuan('putusan');

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
		}elseif(in_array('survei', $this->scopes['scopes'])){
			view()->share('is_survei_tab', 'active show');
		}elseif(in_array('analisa', $this->scopes['scopes'])){
			view()->share('is_analisa_tab', 'active show');
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

			$survei 		= Survei::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->with(['character', 'condition', 'capacity', 'capital', 'jaminan_kendaraan', 'jaminan_tanah_bangunan', 'jaminan_kendaraan.foto', 'jaminan_tanah_bangunan.foto'])->first();

			$analisa 		= Analisa::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();
			$putusan 		= Putusan::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();
			$r_nasabah 		= $this->riwayat_kredit_nasabah($permohonan['nasabah']['nik'], $id);

			view()->share('active_submenu', 'pengajuan');
			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
			HelperController::variable_list_select();

			if ($permohonan['status_terakhir']['status'] == 'permohonan') {
				view()->share('is_active_permohonan', 'active');
				$this->middleware_checker_permohonan($permohonan);
			} elseif ($permohonan['status_terakhir']['status'] == 'survei') {
				view()->share('is_active_survei', 'active');
				$this->middleware_checker_survei($survei);
			} elseif ($permohonan['status_terakhir']['status'] == 'analisa') {
				view()->share('is_active_analisa', 'active');
				$this->middleware_checker_analisa($analisa);
			} elseif ($permohonan['status_terakhir']['status'] == 'putusan') {
				view()->share('is_active_putusan', 'active');
				$this->middleware_checker_putusan($putusan);
			}

			$this->layout->pages 	= view('v2.pengajuan.show', compact('permohonan', 'survei', 'analisa', 'putusan', 'r_nasabah'));
			return $this->layout;

		} catch (Exception $e) {
			return redirect()->route('pengajuan.index', request()->all())->withErrors($e->getMessage());
		}
	}

	public function store($id = null){
		
		try {
			\DB::begintransaction();

			$permohonan 	= Pengajuan::findornew($id);

			if(str_is($permohonan->status_terakhir->status, 'putusan')){
				$returned 	= $this->middleware_store_putusan($permohonan);
			}elseif(str_is($permohonan->status_terakhir->status, 'analisa')){
				$returned 	= $this->middleware_store_analisa($permohonan);
			}elseif(str_is($permohonan->status_terakhir->status, 'survei')){
				$returned 	= $this->middleware_store_survei($permohonan);
			}elseif(str_is($permohonan->status_terakhir->status, 'permohonan') || is_null($id)){
				$returned 	= $this->middleware_store_permohonan($permohonan);
			}

			if($returned instanceof Pengajuan){
				\DB::commit();
				return redirect()->route('pengajuan.show', ['id' => $returned['id'], 'kantor_aktif_id' => request()->get('kantor_aktif_id')]);
			}
		
			\DB::rollback();
			return redirect()->back()->withErrors($returned);
		} catch (Exception $e) {
			\DB::rollback();
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function create(){
		view()->share('active_submenu', 'permohonan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
		HelperController::variable_list_select();
		
		$this->layout->pages 	= view('v2.pengajuan.create');
		return $this->layout;
	}

	public function assign($id = null){
		try {
			\DB::begintransaction();

			$permohonan 	= Pengajuan::findorfail($id);

			$returned 		= [];
			if(str_is($permohonan->status_terakhir->status, 'permohonan')){
				$returned 	= $this->middleware_assign_surveyor($permohonan);
			}elseif(str_is($permohonan->status_terakhir->status, 'survei')){
				$returned 	= $this->middleware_assign_analis($permohonan);
			}elseif(str_is($permohonan->status_terakhir->status, 'analisa')){
				$returned 	= $this->middleware_assign_komite_putusan($permohonan);
			}elseif(str_is($permohonan->status_terakhir->status, 'putusan')){
				$returned 	= $this->middleware_assign_realisasi($permohonan);
			}

			if($returned instanceof Model){
				\DB::commit();
				return redirect()->route('pengajuan.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id'), 'current' => $permohonan->status_terakhir->status]);
			}

			\DB::rollback();
			return redirect()->back()->withErrors($returned);
		} catch (Exception $e) {
			\DB::rollback();
			return redirect()->back()->withErrors($e->getMessage());
		}
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
	
 	public function ajax ()
	{
		$pengajuan 		= Pengajuan::status(request()->get('status'))->kantor(request()->get('kantor_aktif_id'));
		if(request()->has('q'))
		{
			$pengajuan	= $pengajuan->where('p_pengajuan.id', 'like', '%'.request()->get('q').'%');
		}
		$pengajuan 		= $pengajuan->get();

		return response()->json($pengajuan);
	}

	public function print($id, $mode)
	{
		$data['pengajuan']	= Pengajuan::where('id', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->first()->toArray();
		
		$data['survei']		=  Survei::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->with(['character', 'condition', 'capacity', 'capital', 'jaminan_kendaraan', 'jaminan_kendaraan.foto', 'jaminan_tanah_bangunan', 'jaminan_tanah_bangunan.foto', 'surveyor'])->first();
		
		$data['analisa']	= Analisa::where('pengajuan_id', $data['pengajuan']['id'])->first();

		$data['putusan']	= Putusan::where('pengajuan_id', $data['pengajuan']['id'])->first();

		if(!is_null($data['putusan']))
		{
			$tanggal 		= Carbon::createFromFormat('d/m/Y H:i', $data['putusan']['tanggal'])->format('Y-m-d H:i:s');
		}
		else
		{
			$tanggal 		= Carbon::now()->format('Y-m-d H:i:s');
		}

		$pimpinan 			= PenempatanKaryawan::where('kantor_id', request()->get('kantor_aktif_id'))->where('role', 'pimpinan')->where('tanggal_masuk', '>=', $tanggal)->where(function($q)use($tanggal){$q->where('tanggal_keluar', '<=', $tanggal)->orwherenull('tanggal_keluar');})->first();
	
		if($mode=='perjanjian_kredit' && $data['analisa']['jenis_pinjaman']=='pa')
		{
			$mode 	= 'perjanjian_kredit_angsuran';
		}
		elseif($mode=='perjanjian_kredit')
		{
			$mode 	= 'perjanjian_kredit_musiman';
		}

		return view('v2.pengajuan.print.'.$mode, compact('data', 'pimpinan'));
	}
}
