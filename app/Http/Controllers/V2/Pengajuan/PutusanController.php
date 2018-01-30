<?php

namespace App\Http\Controllers\V2\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Analisa;
use Thunderlabid\Pengajuan\Models\Status;

use Thunderlabid\Survei\Models\Survei;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use App\Http\Controllers\V2\Traits\AkunTrait;
use App\Http\Controllers\V2\Traits\PutusanTrait;
use App\Http\Controllers\V2\Traits\PengajuanTrait;

use App\Service\Traits\IDRTrait;

use App\Http\Service\Policy\PerhitunganBunga;

use Exception, Auth, DB, Carbon\Carbon;

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
			$notabayar 	= NotaBayar::where('nomor_kredit', $putusan['nomor_kredit'])->where('jumlah', (0-$total))->first();
			$setoran 	= NotaBayar::where('nomor_kredit', $putusan['nomor_kredit'])->where('jumlah', $ts)->first();

			$angsuran 	= new PerhitunganBunga($putusan['plafon_pinjaman'], $putusan['pengajuan']['kemampuan_angsur'], $putusan['suku_bunga']);

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

			$akun 				= $this->get_akun(request()->get('kantor_aktif_id'));

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
				$objek 		= request()->get('checklists')['objek'];
				$pengikat 	= request()->get('checklists')['pengikat'];

				foreach ($putusan['checklists']['objek'] as $k => $v) {
					if($objek[$k]){
						$data_input['checklists']['objek'][$k]	= 'ada';
					}elseif(!str_is($v, 'cadangkan')){
						$data_input['checklists']['objek'][$k]	= 'tidak_ada';
					}else{
						$data_input['checklists']['objek'][$k]	= 'cadangkan';
					}
				}

				foreach ($putusan['checklists']['pengikat'] as $k => $v) {
					if($pengikat[$k]){
						$data_input['checklists']['pengikat'][$k]	= 'ada';
					}elseif(!str_is($v, 'cadangkan')){
						$data_input['checklists']['pengikat'][$k]	= 'tidak_ada';
					}else{
						$data_input['checklists']['pengikat'][$k]	= 'cadangkan';
					}
				}

				$putusan->fill($data_input);
				$putusan->save();
			}
			elseif(request()->has('status') && !str_is($putusan->pengajuan->status_terakhir, 'realisasi')){
				$status 				= new Status;
				$status->tanggal 		= Carbon::now()->format('d/m/Y H:i');
				$status->progress 		= 'sudah';
				$status->status 		= 'realisasi';
				$status->karyawan 		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
				$status->pengajuan_id 	= $id;
				$status->save();
			}
			elseif(request()->has('nomor_perkiraan')){
				if(request()->has('setoran')){

					//simpan nota bayar
					$total 		= $this->formatMoneyFrom($putusan->provisi) + $this->formatMoneyFrom($putusan->administrasi) + $this->formatMoneyFrom($putusan->legal) + $this->formatMoneyFrom($putusan->biaya_notaris);

					$nb 					= NotaBayar::where('nomor_kredit', $putusan['nomor_kredit'])->where('jumlah', $total)->first();
					if(!$nb){
						$nb 				= new NotaBayar;
						$nb->nomor_faktur  	= NotaBayar::generatenomorfaktur($putusan['nomor_kredit']);
						$nb->tanggal 		= Carbon::now()->format('d/m/Y H:i');
						$nb->nip_karyawan 	= Auth::user()['nip'];
					}

					$nb->nomor_kredit 		= $putusan->nomor_kredit;
					$nb->jumlah 			= $this->formatMoneyTo($total);
					$nb->nomor_perkiraan	= request()->get('nomor_perkiraan');
					$nb->jenis 				= 'setoran_pencairan';
					$nb->save();

					$idx 				= ['provisi', 'administrasi', 'legal', 'biaya_notaris'];

					foreach ($idx as $k => $v) {
						$ad 			= AngsuranDetail::where('nota_bayar_id', $nb->id)->where('nth', 0)->where('tag', $v)->first();
						if(!$ad){
							$ad 		= new AngsuranDetail;
						}
						$ad->nota_bayar_id 	= $nb->id;
						$ad->nomor_kredit 	= $nb->nomor_kredit;
						$ad->tanggal 		= $nb->tanggal;
						$ad->nth 			= 0;
						$ad->tag 			= $v;
						$ad->amount 		= $putusan[$v];
						$ad->description 	= ucwords(str_replace('_', ' ', $v));
						$ad->save();
					}
				}else{
					//simpan nota bayar
					$total 		= $this->formatMoneyFrom($putusan->plafon_pinjaman);

					$nb 					= NotaBayar::where('nomor_kredit', $putusan['nomor_kredit'])->where('jumlah', (0-$total))->first();
					if(!$nb){
						$nb 				= new NotaBayar;
						$nb->nomor_faktur  	= NotaBayar::generatenomorfaktur($putusan['nomor_kredit']);
						$nb->tanggal 		= Carbon::now()->format('d/m/Y H:i');
						$nb->nip_karyawan 	= Auth::user()['nip'];
					}

					$nb->nomor_kredit 		= $putusan->nomor_kredit;
					$nb->jumlah 			= $this->formatMoneyTo(0 - $total);
					$nb->nomor_perkiraan	= request()->get('nomor_perkiraan');
					$nb->jenis 				= 'pencairan';
					$nb->save();

					//angsuran detail
					$ad 			= AngsuranDetail::where('nota_bayar_id', $nb->id)->where('nth', 0)->where('tag', 'realisasi')->first();
					if(!$ad){
						$ad 		= new AngsuranDetail;
					}
					$ad->nota_bayar_id 	= $nb->id;
					$ad->nomor_kredit 	= $nb->nomor_kredit;
					$ad->tanggal 		= $nb->tanggal;
					$ad->nth 			= 0;
					$ad->tag 			= 'realisasi';
					$ad->amount 		= $nb->jumlah;
					$ad->description 	= 'Realisasi Kredit';
					$ad->save();
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
					$notabayar 	= NotaBayar::where('nomor_kredit', $putusan['nomor_kredit'])->where('jumlah', (0-$total))->first();

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
					$notabayar 	= NotaBayar::where('nomor_kredit', $putusan['nomor_kredit'])->where('jumlah', $total)->with(['details'])->first();
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
