<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\SuratPeringatan;

use Exception, Auth, Carbon\Carbon;

class TunggakanController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index() 
	{
		$today 		= Carbon::now();

		if(request()->has('q')){
			$today	= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$tunggakan 	= AngsuranDetail::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($today)->with(['kredit', 'kredit.penagihan'])->orderby('tanggal', 'asc')->get();

		view()->share('is_aktif_tab', 'show active');

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.tunggakan.index', compact('tunggakan'));
		return $this->layout;
	}

	public function show($id) 
	{
		$today	= Carbon::now();
		$nk 	= request()->get('nomor_kredit');
		// $tanggal= request()->get('tanggal');
		$tanggal= Carbon::now()->format('d/m/Y H:i');

		$tunggakan 	= AngsuranDetail::where('nomor_kredit', $nk)->wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($today)->with(['kredit', 'kredit.penagihan'])->orderby('tanggal', 'asc')->first();

		try {
			if(!$tunggakan && !is_null($tunggakan['should_issue_surat_peringatan']['keluarkan'])){
				throw new Exception("Tidak ada tunggakan", 1);
			}

			$new_sp 				= new SuratPeringatan; 
			$new_sp->nomor_kredit 	= $tunggakan['nomor_kredit'];
			$new_sp->nth 			= $tunggakan['nth'];
			$new_sp->tanggal 		= $tanggal;
			$new_sp->tag 			= $tunggakan['should_issue_surat_peringatan']['keluarkan'];
			$new_sp->nip_karyawan 	= Auth::user()['nip'];
			$new_sp->save();
			return redirect()->back();
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}
}
