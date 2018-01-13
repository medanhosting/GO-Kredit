<?php

namespace App\Http\Controllers\V2\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Status;

use Thunderlabid\Survei\Models\Survei;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;


use App\Http\Controllers\V2\Traits\AkunTrait;
use App\Http\Controllers\V2\Traits\PengajuanTrait;

use App\Service\Traits\IDRTrait;

use Exception, Auth, DB;

class PutusanController extends Controller
{
	use PengajuanTrait;
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
			$putusan 			= Putusan::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();
			$survei 			= Survei::where('pengajuan_id', $id)->first();

			$akun 	= $this->get_akun(request()->get('kantor_aktif_id'));

			if(request()->has('current')){
				switch (request()->get('current')) {
					case 'bukti_pencairan':
						view()->share('is_bukti_pencairan_tab', 'active show');
						break;
					default:
						view()->share('is_legalitas_tab', 'active show');
						break;
				}			
			}else{
				view()->share('is_legalitas_tab', 'active show');
			}

			view()->share('active_submenu', 'putusan');
			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

			$this->layout->pages 	= view('v2.putusan.show', compact('putusan', 'survei', 'akun'));
			return $this->layout;
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function store($id = null)
	{
		try {
			\DB::beginTransaction();
			$putusan					= Putusan::where('pengajuan_id', $id)->wherehas('pengajuan', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->orderby('tanggal', 'desc')->first();

			if(request()->has('checklists')){
				$data_input['checklists'] 	= request()->get('checklists');
				$putusan->fill($data_input);
				$putusan->save();
			}

			if(request()->has('tanggal_realisasi')){
				$status 				= new Status;
				$status->tanggal 		= request()->get('tanggal_realisasi');
				$status->progress 		= 'sedang';
				$status->status 		= 'realisasi';
				$status->karyawan 		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
				$status->pengajuan_id 	= $id;
				$status->save();
			}

			if(request()->has('tanggal_pencairan') && !str_is($putusan->pengajuan->status_terakhir, 'realisasi')){
				$status 				= new Status;
				$status->tanggal 		= request()->get('tanggal_pencairan');
				$status->progress 		= 'sudah';
				$status->status 		= 'realisasi';
				$status->karyawan 		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
				$status->pengajuan_id 	= $id;
				$status->save();

				//simpan nota bayar
				$kredit 	= Aktif::where('nomor_pengajuan', $id)->first();
				$total 		= $this->formatMoneyFrom($putusan->plafon_pinjaman);

				$nb 				= new NotaBayar;
				$nb->nomor_kredit 	= $kredit['nomor_kredit'];
				$nb->nomor_faktur  	= NotaBayar::generatenomorfaktur($kredit['nomor_kredit']);
				$nb->tanggal 		= $status->tanggal;
				$nb->nip_karyawan 	= Auth::user()['nip'];
				$nb->jumlah 		= $this->formatMoneyTo(0 - $total);
				$nb->kode_akun		= request()->get('kode_akun');
				$nb->save();

				//angsuran detail
				$ad 		= new AngsuranDetail;
				$ad->nota_bayar_id 	= $nb->id;
				$ad->nomor_kredit 	= $nb->nomor_kredit;
				$ad->tanggal 		= $nb->tanggal;
				$ad->nth 			= 0;
				$ad->tag 			= 'pencairan';
				$ad->amount 		= $nb->jumlah;
				$ad->description 	= 'Pencairan Kredit';
				$ad->save();
			}

			\DB::commit();
			return redirect(route('putusan.show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]));

		} catch (Exception $e) {
			\DB::rollback();
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
			$survei		= Survei::where('pengajuan_id', $id)->first();

			view()->share('survei', $survei);
			view()->share('putusan', $putusan);
			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

			return view('v2.print.putusan.bukti_realisasi');
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}
}
