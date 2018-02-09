<?php

namespace App\Http\Controllers\V2\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Analisa;
use Thunderlabid\Pengajuan\Models\Status;

use Thunderlabid\Survei\Models\Survei;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;
use Thunderlabid\Finance\Models\Jurnal;

use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use App\Http\Controllers\V2\Traits\AkunTrait;
use App\Http\Controllers\V2\Traits\PutusanTrait;
use App\Http\Controllers\V2\Traits\PengajuanTrait;

use App\Service\Traits\IDRTrait;

use App\Http\Service\Policy\PerhitunganBunga;

use Exception, Auth, DB, Carbon\Carbon, Config;

use App\Http\Middleware\ScopeMiddleware;
use App\Http\Middleware\RequiredPasswordMiddleware;

class PutusanController extends Controller
{
	use PengajuanTrait;
	use PutusanTrait;
	use AkunTrait;
	use IDRTrait;
	
	public function __construct()
	{
		parent::__construct();

		$this->middleware('scope:operasional.pengajuan')->only(['index', 'show']);
		$this->middleware('scope:realisasi')->only(['store', 'print']);
	}


	public function __call($name, $arguments)
	{
		if(str_is('*middleware_*', $name)){
			if(in_array($name, ['middleware_store_checklists', 'middleware_validasi_checklists'])){
				ScopeMiddleware::check('realisasi');
			}
			if(in_array($name, ['middleware_store_setoran_realisasi', 'middleware_store_realisasi'])){
				ScopeMiddleware::check('pencairan');
			}
			if(in_array($name, ['middleware_validasi_checklists', 'middleware_store_setoran_realisasi', 'middleware_store_realisasi'])){
				RequiredPasswordMiddleware::check();
			}
			
			return call_user_func_array([$this, str_replace('middleware_', '', $name)], $arguments);
		}
	}

	public function index () 
	{
		$setuju 	= $this->get_pengajuan(['setuju', 'realisasi']);
		$tolak 		= $this->get_pengajuan('tolak');

		if(request()->has('current')){
			switch (request()->get('current')) {
				case 'tolak':
					view()->share('is_tolak_tab', 'active show');
					break;
				case 'setuju':
					view()->share('is_setuju_tab', 'active show');
					break;
			}			
		}else{
			view()->share('is_setuju_tab', 'active show');
		}

		view()->share('active_submenu', 'putusan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.putusan.index', compact('setuju', 'tolak'));
		return $this->layout;
	}

	public function show($id){
		try {
			$putusan 	= Putusan::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();
			
			$total 		= $this->formatMoneyFrom($putusan->plafon_pinjaman);
			$ts 		= $this->formatMoneyFrom($putusan->provisi) + $this->formatMoneyFrom($putusan->administrasi) + $this->formatMoneyFrom($putusan->legal) + $this->formatMoneyFrom($putusan->biaya_notaris);
			$survei 	= Survei::where('pengajuan_id', $id)->first();
			
			$notabayar 	= NotaBayar::where('morph_reference_id', $putusan['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('jenis', 'pencairan')->first();
			$setoran 	= NotaBayar::where('morph_reference_id', $putusan['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('jenis', 'setoran_pencairan')->first();

			$angsuran 	= new PerhitunganBunga($putusan['plafon_pinjaman'], 'Rp 0', $putusan['suku_bunga'], null, null, null, $putusan['jangka_waktu']);

			if(str_is($putusan['pengajuan']['analisa']['jenis_pinjaman'], 'pa')){
				$angsuran 		= $angsuran->pa();
				$is_pa 			= true;
			}else{
				$is_pa 			= false;
				$angsuran 		= $angsuran->pt();
			}

			if(!$notabayar){
				$tanggal_p 	= Carbon::now()->format('d/m/Y');
			}else{
				$tanggal_p 	= Carbon::createFromFormat('d/m/Y H:i', $notabayar['tanggal'])->format('d/m/Y');
			}

			if(!$setoran){
				$tanggal_s 	= Carbon::now()->format('d/m/Y');
			}else{
				$tanggal_s 	= Carbon::createFromFormat('d/m/Y H:i', $setoran['tanggal'])->format('d/m/Y');
			}

			$akun 				= $this->get_akun(request()->get('kantor_aktif_id'), Config::get('finance.nomor_rekening_aktif'));

			if(request()->has('current')){
				switch (request()->get('current')) {
					case 'pencairan':
						view()->share('is_active_pencairan', 'active show');
						break;
					case 'setoran':
						view()->share('is_active_setoran', 'active show');
						break;
					default:
						$this->checker_realisasi($putusan['checklists']);
						view()->share('is_active_realisasi', 'active show');
						break;
				}			
			}else{
				$this->checker_realisasi($putusan['checklists']);
				view()->share('is_active_realisasi', 'active show');
			}

			view()->share('active_submenu', 'putusan');
			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

			$this->layout->pages 	= view('v2.putusan.show', compact('putusan', 'survei', 'akun', 'notabayar', 'angsuran', 'is_pa', 'tanggal_p', 'setoran', 'ts', 'tanggal_s'));
			return $this->layout;
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function store($id = null)
	{
		try {
			DB::beginTransaction();
			$putusan					= Putusan::where('pengajuan_id', $id)->wherehas('pengajuan', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->orderby('tanggal', 'desc')->first();

			if(request()->has('checklists')){
				$this->middleware_store_checklists($putusan);
			}
			elseif(request()->has('status') && !str_is($putusan->pengajuan->status_terakhir, 'realisasi')){
				$this->middleware_validasi_checklists($id);
			}
			elseif(request()->has('nomor_perkiraan')){
				if(request()->has('setoran')){
					$this->middleware_store_setoran_realisasi($putusan);
				}else{
					$this->middleware_store_realisasi($putusan);
				}
			}

			DB::commit();
			return redirect(route('putusan.show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]));

		} catch (Exception $e) {
			DB::rollback();
			return redirect()->back()->withErrors($e->getMessage());
		}

	}

	public function update($id)
	{
		return $this->store($id);
	}

	public function print($id) 
	{
		try {
			$putusan	= Putusan::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();

			view()->share('putusan', $putusan);
			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

			$mode 		= request()->get('mode');
			switch (strtolower($mode)) {
				case 'bukti_realisasi':
					$survei 	= Survei::where('pengajuan_id', $id)->first();
					$total 		= $this->formatMoneyFrom($putusan->plafon_pinjaman);
					$notabayar 	= NotaBayar::where('morph_reference_id', $putusan['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('jenis', 'pencairan')->first();

					$angsuran 	= new PerhitunganBunga($putusan['plafon_pinjaman'], $putusan['pengajuan']['kemampuan_angsur'], $putusan['suku_bunga']);

					if(str_is($putusan['pengajuan']['analisa']['jenis_pinjaman'], 'pa')){
						$angsuran 		= $angsuran->pa();
						$is_pa 			= true;
					}else{
						$is_pa 			= false;
						$angsuran 		= $angsuran->pt();
					}
					$tanggal_p 	= Carbon::createFromFormat('d/m/Y H:i', $notabayar['tanggal'])->format('d/m/Y');
					
					view()->share('is_pa', $is_pa);
					view()->share('survei', $survei);
					view()->share('notabayar', $notabayar);
					view()->share('angsuran', $angsuran);
					view()->share('tanggal_p', $tanggal_p);

					return view('v2.putusan.print.bukti_realisasi');

					break;
				case 'setoran_realisasi':
					$total 		= $this->formatMoneyFrom($putusan->provisi) + $this->formatMoneyFrom($putusan->administrasi) + $this->formatMoneyFrom($putusan->legal) + $this->formatMoneyFrom($putusan->biaya_notaris);
					$notabayar 	= NotaBayar::where('morph_reference_id', $putusan['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('jenis', 'setoran_pencairan')->first();
					$tanggal_s 	= Carbon::createFromFormat('d/m/Y H:i', $notabayar['tanggal'])->format('d/m/Y');

					view()->share('tanggal_s', $tanggal_s);
					view()->share('notabayar', $notabayar);

					return view('v2.putusan.print.bukti_setoran_realisasi');
					break;
				default:
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

					$angsuran 	= new PerhitunganBunga($putusan['plafon_pinjaman'], $putusan['pengajuan']['kemampuan_angsur'], $putusan['suku_bunga']);

					if(str_is($data['analisa']['jenis_pinjaman'], 'pa')){
						$angsuran 		= $angsuran->pa();
					}else{
						$angsuran 		= $angsuran->pt();
					}

					view()->share('data', $data);
					view()->share('angsuran', $angsuran);
					view()->share('pimpinan', $pimpinan);

					return view('v2.putusan.print.'.$mode);
				break;
			}
			abort(404);
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}
}
