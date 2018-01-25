<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\SuratPeringatan;

use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use Exception, Auth, Carbon\Carbon;

class TunggakanController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->middleware('scope:angsuran');
	}

	public function index() 
	{
		$today 		= Carbon::now();

		if(request()->has('q')){
			$today	= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$tunggakan 	= AngsuranDetail::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($today)->with(['kredit', 'kredit.penagihan'])->orderby('tanggal', 'asc')->get();

		view()->share('is_aktif_tab', 'show active');

		view()->share('active_submenu', 'tunggakan');
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

	public function print($id)
	{
		try {
			$surat 	= SuratPeringatan::where('nomor_kredit', $id)->where('id', request()->get('sp_id'))->first();
			$tanggal_surat 	= Carbon::createFromFormat('d/m/Y H:i', $surat->tanggal);
			
			$t_tunggakan 	= AngsuranDetail::HitungTunggakanBeberapaWaktuLalu($tanggal_surat)->where('nomor_kredit', $id)->selectraw('count(nth) as jumlah_tunggakan')->first();
			 
			$before 		= AngsuranDetail::TunggakanBeberapaWaktuLalu($tanggal_surat)
			->where('nomor_kredit', $id)
			->whereIn('tag', ['denda', 'restitusi_denda'])
			->selectraw(\DB::raw('SUM(IF(tag="denda",amount,IF(tag="restitusi_denda",amount,0))) as denda'))
			->groupby('nomor_kredit')
			->first();

			$middle 		= AngsuranDetail::where('tanggal', '<=', $tanggal_surat->format('Y-m-d H:i:s'))
			->where('nomor_kredit', $id)
			->whereIn('tag', ['titipan', 'pengambilan_titipan'])
			->selectraw(\DB::raw('SUM(IF(tag="titipan",amount,IF(tag="pengambilan_titipan",amount,0))) as titipan'))
			->groupby('nomor_kredit')
			->first();

			$after 			= AngsuranDetail::where('tanggal', '>', $tanggal_surat->format('Y-m-d H:i:s'))
			->where('nomor_kredit', $id)
			->whereIn('tag', ['pokok', 'bunga'])
			->selectraw(\DB::raw('SUM(IF(tag="pokok",amount,0)) as pokok'))
			->selectraw(\DB::raw('SUM(IF(tag="bunga",amount,0)) as bunga'))
			->groupby('nomor_kredit')
			->first();

			$pimpinan 		= PenempatanKaryawan::where('kantor_id', request()->get('kantor_aktif_id'))->where('role', 'pimpinan')->where('tanggal_masuk', '>=', $tanggal_surat->format('Y-m-d H:i:s'))->where(function($q)use($tanggal_surat){$q->where('tanggal_keluar', '<=', $tanggal_surat->format('Y-m-d H:i:s'))->orwherenull('tanggal_keluar');})->first();

			view()->share('surat', $surat);
			view()->share('tanggal_surat', $tanggal_surat);
			view()->share('t_tunggakan', $t_tunggakan);
			view()->share('before', $before);
			view()->share('middle', $middle);
			view()->share('after', $after);
			view()->share('pimpinan', $pimpinan);

			if (str_is('surat_peringatan*', $surat->tag)) {
				view()->share('html', ['title' => 'Surat Peringatan | ' . config()->get('app.name') . '.com']);

				return view('v2.kredit.print.surat_peringatan');
			} else {
				view()->share('html', ['title' => 'Somasi | ' . config()->get('app.name') . '.com']);

				return view('v2.kredit.print.surat_somasi');
			}
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}
}
