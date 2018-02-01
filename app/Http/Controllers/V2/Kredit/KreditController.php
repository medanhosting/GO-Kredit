<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Kredit\Models\SuratPeringatan;
use Thunderlabid\Kredit\Models\PermintaanRestitusi;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\MutasiJaminan;

use Thunderlabid\Finance\Models\NotaBayar;
use Exception, DB, Auth, Carbon\Carbon, Config;

use App\Service\Traits\IDRTrait;

use App\Http\Controllers\V2\Traits\AkunTrait;
use App\Http\Controllers\V2\Traits\KreditTrait;

use App\Http\Service\Policy\BayarDenda;

class KreditController extends Controller
{
	use KreditTrait;
	use AkunTrait;
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();

		$this->middleware('scope:operasional.kredit')->only(['index', 'show']);

		$this->middleware('scope:tagihan')->only(['store_tagihan']);
		$this->middleware('scope:angsuran')->only(['store_angsuran', 'store_denda']);
	}

	public function index () 
	{
		$aktif 		= Aktif::kantor(request()->get('kantor_aktif_id'))->PembayaranBerikut()->with(['jaminan' => function($q){$q->selectraw('max(id) as id, max(nomor_kredit) as nomor_kredit, max(tag) as tag, max(tanggal) as tanggal, max(dokumen) as dokumen')->groupby('nomor_jaminan');}])->paginate(15, ['*'], 'aktif');

		view()->share('is_aktif_tab', 'show active');

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.index', compact('aktif'));
		return $this->layout;
	}

	public function show($id) 
	{
		$aktif	= Aktif::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->PembayaranBerikut()->first();
		$akun	= $this->get_akun(request()->get('kantor_aktif_id'));
		$a_tt	= $this->get_akun(request()->get('kantor_aktif_id'), Config::get('finance.nomor_perkiraan_titipan'));
		$a_dd	= $this->get_akun(request()->get('kantor_aktif_id'), Config::get('finance.nomor_perkiraan_denda'));
		$today	= Carbon::now();

		//PANEL ANGSURAN. DATA STAT, DATA DENDA, DATA ANGSURAN
		//DATA STAT
		$stat['sisa_hutang']		= JadwalAngsuran::wherenull('nomor_faktur')->where('nomor_kredit', $aktif['nomor_kredit'])->sum('jumlah');
		// $stat['total_titipan']		= AngsuranDetail::whereIn('tag', ['titipan', 'pengambilan_titipan'])->where('nomor_kredit', $aktif['nomor_kredit'])->where('tanggal', '<=', $today->format('Y-m-d H:i:s'))->wherehas('notabayar', function($q){$q->where('nomor_perkiraan', Config::get('finance.nomor_perkiraan_titipan'));})->sum('amount');
		// $stat['total_denda']		= AngsuranDetail::whereIn('tag', ['denda', 'restitusi_denda'])->wherenull('nomor_faktur')->where('nomor_kredit', $aktif['nomor_kredit'])->sum('amount');

		//DATA ANGSURAN
		$angsuran 	= JadwalAngsuran::where('nomor_kredit', $aktif['nomor_kredit'])->get();
		$notabayar 	= NotaBayar::where('morph_reference_id', $aktif['nomor_kredit'])->where('morph_reference_tag', 'kredit')->get();
		// $titipan 	= NotaBayar::where('nomor_kredit', $aktif['nomor_kredit'])->where('nomor_perkiraan', Config::get('finance.nomor_perkiraan_titipan_kolektor'))->get();
	
		// //DATA DENDA, PERMINTAAN RESTITUSI
		// $denda 		= AngsuranDetail::displayingdenda()->where('nomor_kredit', $aktif['nomor_kredit'])->get();
		// $stat['total_restitusi'] 	= PermintaanRestitusi::where('nomor_kredit', $aktif['nomor_kredit'])->wherenull('is_approved')->sum('amount');
		// $restitusi 	= PermintaanRestitusi::where('nomor_kredit', $aktif['nomor_kredit'])->wherenull('is_approved')->first();
		// $r3d 		= BayarDenda::hitung_r3d(Carbon::now()->format('d/m/Y H:i'), $aktif['persentasi_denda']);

		// //PANEL TUNGGAKAN/PENAGIHAN
		// $stat['last_pay'] 			= NotaBayar::where('nomor_kredit', $aktif['nomor_kredit'])->orderby('tanggal', 'desc')->first();
		$stat['total_tunggakan']	= JadwalAngsuran::wherenull('nomor_faktur')->where('nomor_kredit', $aktif['nomor_kredit'])->where('tanggal', '<=', $today->format('Y-m-d H:i:s'))->sum('jumlah');
		
		$stat['jumlah_tunggakan']	= JadwalAngsuran::wherenull('nomor_faktur')->where('nomor_kredit', $aktif['nomor_kredit'])->where('tanggal', '<=', $today->format('Y-m-d H:i:s'))->count();

		// $stat['total_hutang']		= AngsuranDetail::whereIn('tag', ['pokok', 'bunga'])->where('nomor_kredit', $aktif['nomor_kredit'])->sum('amount');

		// $stat['last_sp'] 			= SuratPeringatan::where('nomor_kredit', $aktif['nomor_kredit'])->orderby('tanggal', 'desc')->orderby('created_at', 'desc')->first();

		// //DATA SP
		// $suratperingatan 	= SuratPeringatan::where('nomor_kredit', $aktif['nomor_kredit'])->with(['penagihan', 'penagihan.notabayar'])->get();

		// //PANEL JAMINAN
		// $ids 		= MutasiJaminan::where('nomor_kredit', $aktif['nomor_kredit'])->selectraw('max(id) as id, max(tanggal) as tanggal')->groupby('nomor_jaminan')->orderby('tanggal', 'desc')->get()->toArray();
		// $jaminan 	= MutasiJaminan::wherein('id', array_column($ids, 'id'))->get();

		if(request()->get('current')){
			switch (strtolower(request()->get('current'))) {
				case 'angsuran':
					view()->share('is_angsuran_tab', 'show active');
					break;
				case 'denda':
					view()->share('is_denda_tab', 'show active');
					break;
				case 'tunggakan':
					view()->share('is_tunggakan_tab', 'show active');
					break;
				case 'penagihan':
					view()->share('is_penagihan_tab', 'show active');
					break;
				case 'jaminan':
					view()->share('is_jaminan_tab', 'show active');
					break;
				default:
					view()->share('is_kredit_tab', 'show active');
					break;
			}
		}else{
			view()->share('is_kredit_tab', 'show active');
		}

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		view()->share('akun', $akun);
		view()->share('aktif', $aktif);
		view()->share('angsuran', $angsuran);
		view()->share('notabayar', $notabayar);
		view()->share('titipan', $titipan);
		view()->share('jaminan', $jaminan);
		view()->share('suratperingatan', $suratperingatan);
		view()->share('denda', $denda);
		view()->share('restitusi', $restitusi);
		view()->share('stat', $stat);
		view()->share('r3d', $r3d);

		view()->share('kredit_id', $id);

		$this->layout->pages 	= view('v2.kredit.show', compact('a_tt'));
		return $this->layout;
	}

	public function update($id) 
	{
		try {
			$aktif 		= Aktif::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->first();

			DB::BeginTransaction();
			switch (request()->get('current')) {
				case 'tagihan':
					$this->store_tagihan($aktif);
					break;
				case 'penerimaan_titipan_tagihan':
					$this->penerimaan_titipan_tagihan($aktif);
					break;
				case 'bayar_sebagian':
					$this->store_bayar_sebagian($aktif);
					break;
				case 'permintaan_restitusi':
					$this->store_permintaan_restitusi($aktif);
					break;
				case 'validasi_restitusi':
					$this->store_validasi_restitusi($aktif);
					break;
				case 'denda':
					$this->store_denda($aktif);
					break;
				default:
					$this->store_angsuran($aktif);
					break;
			}
			DB::commit();
			return redirect()->back();
		} catch (Exception $e) {
			DB::rollback();
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function store(){
		return $this->update(request()->get('id'));
	}
}
