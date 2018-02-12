<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Kredit\Models\SuratPeringatan;
use Thunderlabid\Kredit\Models\PermintaanRestitusi;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\MutasiJaminan;

use Thunderlabid\Finance\Models\Jurnal;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;
use Exception, DB, Auth, Carbon\Carbon, Config;

use App\Service\Traits\IDRTrait;

use App\Http\Controllers\V2\Traits\AkunTrait;
use App\Http\Controllers\V2\Traits\KreditTrait;

use App\Http\Service\Policy\BayarDenda;

use App\Http\Middleware\ScopeMiddleware;
use App\Http\Middleware\RequiredPasswordMiddleware;

use App\Service\System\Calculator;

class KreditController extends Controller
{
	use KreditTrait;
	use AkunTrait;
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();

		$this->middleware('scope:operasional|kredit|angsuran|penagihan|restitusi|jaminan|mutasi_jaminan')->only(['index', 'show']);
		
		$this->middleware('limit_date:'.implode('|', $this->scopes['scopes']))->only(['update', 'store']);
	}

	public function __call($name, $arguments)
	{
		if(str_is('*middleware_*', $name)){
			if(in_array($name, ['middleware_store_angsuran', 'middleware_store_denda', 'middleware_store_bayar_sebagian'])){
				ScopeMiddleware::check('angsuran');
			}
			if(in_array($name, ['middleware_store_tagihan', 'middleware_penerimaan_titipan_tagihan'])){
				ScopeMiddleware::check('penagihan');
			}
			if(in_array($name, ['middleware_store_permintaan_restitusi', 'middleware_store_validasi_restitusi'])){
				ScopeMiddleware::check('restitusi');
			}
			// RequiredPasswordMiddleware::check();
			
			return call_user_func_array([$this, str_replace('middleware_', '', $name)], $arguments);
		}
	}

	public function index () 
	{
		$aktif 		= Aktif::kantor(request()->get('kantor_aktif_id'))->PembayaranBerikut()->with(['jaminan']);

		if (request()->has('q'))
		{
			$cari	= request()->get('q');
			$regexp = preg_replace("/-+/",'[^A-Za-z0-9_]+',$cari);
			$aktif	= $aktif->where(function($q)use($regexp)
			{				
				$q
				->whereRaw(DB::raw('nasabah REGEXP "'.$regexp.'"'));
			});
		}

		if (request()->has('jaminan'))
		{
			$cari	= request()->get('jaminan');
			switch (strtolower($cari)) {
				case 'jaminan-bpkb':
					$aktif 	= $aktif->wherehas('jaminan', function($q){$q->where('dokumen->jenis', 'bpkb');});
					break;
				case 'jaminan-sertifikat':
					$aktif 	= $aktif->wherehas('jaminan', function($q){$q->where('dokumen->jenis', 'shgb')->orwhere('dokumen->jenis', 'shm');});
					break;
			}
		}

		if (request()->has('pinjaman'))
		{
			$cari	= request()->get('pinjaman');
			switch (strtolower($cari)) {
				case 'pinjaman-a':
					$aktif 	= $aktif->where('jenis_pinjaman', 'pa');
					break;
				case 'pinjaman-t':
					$aktif 	= $aktif->where('jenis_pinjaman', 'pt');
					break;
			}
		}

		if (request()->has('sort')){
			$sort	= request()->get('sort');
			switch (strtolower($sort)) {
				case 'nama-desc':
					$aktif 	= $aktif->orderby('nasabah->nama', 'desc');
					break;
				case 'pinjaman-asc':
					$aktif 	= $aktif->orderby('plafon_pinjaman', 'asc');
					break;
				case 'pinjaman-desc':
					$aktif 	= $aktif->orderby('plafon_pinjaman', 'desc');
					break;
				default :
					$aktif 	= $aktif->orderby('nasabah->nama', 'asc');
					break;
			}
		}else{
			$aktif 	= $aktif->orderby('nasabah->nama', 'asc');
		}


		$aktif 		= $aktif->paginate(15, ['*'], 'aktif');

		view()->share('is_aktif_tab', 'show active');

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.index', compact('aktif'));
		return $this->layout;
	}

	public function show($id) 
	{
		$aktif	= Aktif::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->PembayaranBerikut()->first();
		$akun	= $this->get_akun(request()->get('kantor_aktif_id'), Config::get('finance.nomor_rekening_aktif'));
		$a_tt	= $this->get_akun(request()->get('kantor_aktif_id'), Config::get('finance.nomor_perkiraan_titipan'));
		$a_dd	= $this->get_akun(request()->get('kantor_aktif_id'), Config::get('finance.nomor_perkiraan_denda'));
		$today		= Carbon::now();
		$tomorrow	= Carbon::now()->adddays(1);

		//1. PANEL ANGSURAN

		//a. STAT SISA HUTANG
		$stat['sisa_hutang']		= Calculator::hutangBefore($aktif['nomor_kredit'], $tomorrow);

		//b. STAT ANGSURAN JT
		$stat['angsuran_bulanan']	= JadwalAngsuran::where('nomor_kredit', $aktif['nomor_kredit'])->orderby('nth', 'asc')->where('nth', 1)->sum('jumlah');
		$stat['total_tunggakan']	= Calculator::piutangBefore($aktif['nomor_kredit'], $tomorrow);
		$stat['jumlah_tunggakan']	= floor($stat['total_tunggakan']/$stat['angsuran_bulanan']);

		//c. STAT TITIPAN
		$stat['total_titipan']		= Calculator::titipanBefore($aktif['nomor_kredit'], $tomorrow);
		$stat['jumlah_titipan']		= floor($stat['total_titipan']/$stat['angsuran_bulanan']);

		//d. Bukti Transaksi
		$notabayar 	= NotaBayar::where('morph_reference_id', $aktif['nomor_kredit'])->where('morph_reference_tag', 'kredit')->whereIn('jenis', ['angsuran', 'angsuran_sementara'])->get();
		
		//e. NTH Angsuran
		$sisa_angsuran 	= JadwalAngsuran::wherenull('nomor_faktur')->where('nomor_kredit', $aktif['nomor_kredit'])->selectraw('jumlah as total')->orderby('nth', 'asc')->get();


		//2. PANEL DENDA
		//a. stat denda
		$stat['total_denda']	= Calculator::dendaBefore($aktif['nomor_kredit'], $tomorrow);

		//b. total permintaan restitusi
		$restitusi 	= PermintaanRestitusi::where('nomor_kredit', $aktif['nomor_kredit'])->wherenull('is_approved')->first();

		if($restitusi){
			view()->share('is_restitusi_tab', 'show active');
		}else{
			view()->share('is_bayar_denda_tab', 'show active');
		}

		//c. restitusi 3d 
		$r3d 		= Calculator::restitusi3DBefore($aktif['nomor_kredit'], $tomorrow);

		//d. bukti denda
		$denda 	= NotaBayar::where('morph_reference_id', $aktif['nomor_kredit'])->where('morph_reference_tag', 'kredit')->whereIn('jenis', ['denda', 'restitusi_denda'])->selectraw('*')->selectraw('abs(jumlah) as jumlah')->get();

		//2. PANEL PENAGIHAN
		//a. stat last pay
		$stat['last_pay']	= NotaBayar::where('morph_reference_id', $aktif['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('tanggal', '<', $today->format('Y-m-d H:i:s'))->orderby('tanggal', 'desc')->first();
		
		//b. stat last SP
		$stat['last_sp']	= SuratPeringatan::where('nomor_kredit', $aktif['nomor_kredit'])->orderby('tanggal', 'desc')->where('tanggal', '<', $today->format('Y-m-d H:i:s'))->orderby('created_at', 'desc')->first();

		//c. riwayat SP
		$suratperingatan 	= SuratPeringatan::where('nomor_kredit', $aktif['nomor_kredit'])->with(['penagihan'])->get();

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
					view()->share('is_angsuran_tab', 'show active');
					break;
			}
		}else{
			view()->share('is_angsuran_tab', 'show active');
		}

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		view()->share('akun', $akun);
		view()->share('aktif', $aktif);
		view()->share('angsuran', $angsuran);
		view()->share('sisa_angsuran', $sisa_angsuran);
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
					$this->middleware_store_tagihan($aktif);
					break;
				case 'penerimaan_titipan_tagihan':
					$this->middleware_penerimaan_titipan_tagihan($aktif);
					break;
				case 'part':
					$this->middleware_store_bayar_sebagian($aktif);
					break;
				case 'permintaan_restitusi':
					$this->middleware_store_permintaan_restitusi($aktif);
					break;
				case 'validasi_restitusi':
					$this->middleware_store_validasi_restitusi($aktif);
					break;
				case 'denda':
					$this->middleware_store_denda($aktif);
					break;
				default:
					$this->middleware_store_angsuran($aktif);
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
