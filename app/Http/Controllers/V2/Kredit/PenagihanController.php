<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Penagihan;

use Exception, Auth, Carbon\Carbon;

class PenagihanController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index() 
	{
		$start 		= Carbon::now()->startofday();
		$end 		= Carbon::now()->endofday();

		if(request()->has('start')){
			$start	= Carbon::createFromFormat('d/m/Y', request()->get('start'))->startofday();
		}
		if(request()->has('end')){
			$end	= Carbon::createFromFormat('d/m/Y', request()->get('end'))->endofday();
		}

		$penagihan 	= Penagihan::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungNotaBayar()->where('tanggal', '>=', $start->format('Y-m-d H:i:s'))->where('tanggal', '<=', $end->format('Y-m-d H:i:s'))->orderby('tanggal', 'asc')->get();

		view()->share('is_aktif_tab', 'show active');

		view()->share('active_submenu', 'penagihan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.penagihan.index', compact('penagihan'));
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
