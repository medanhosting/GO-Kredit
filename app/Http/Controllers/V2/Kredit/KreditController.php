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
use App\Http\Middleware\LimitDateMiddleware;
use App\Http\Middleware\LimitOneMillionMiddleware;
use App\Http\Middleware\RequiredPasswordMiddleware;

use App\Service\System\Calculator;
use App\Service\System\PerhitunganBayar;

class KreditController extends Controller
{
	use KreditTrait;
	use AkunTrait;
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();

		$this->middleware('scope:'.implode('|', $this->acl_menu['kredit.kredit']))->only(['index', 'show']);
	}

	public function __call($name, $arguments)
	{
		if(str_is('*middleware_*', $name)){
			if(in_array($name, ['middleware_store_angsuran', 'middleware_store_denda', 'middleware_store_bayar_sebagian', 'middleware_penerimaan_kas_kolektor'])){
				ScopeMiddleware::check('angsuran');
				LimitDateMiddleware::check(implode('|', $this->scopes['scopes']), 'angsuran');
			}
			if(in_array($name, ['middleware_store_tagihan'])){
				ScopeMiddleware::check('tagihan');
				LimitDateMiddleware::check(implode('|', $this->scopes['scopes']), 'tagihan');
			}
			if(in_array($name, ['middleware_store_permintaan_restitusi'])){
				ScopeMiddleware::check('restitusi');
				LimitDateMiddleware::check(implode('|', $this->scopes['scopes']), 'restitusi');
			}
			if(in_array($name, ['middleware_store_validasi_restitusi'])){
				ScopeMiddleware::check('validasi');
				LimitDateMiddleware::check(implode('|', $this->scopes['scopes']));
				LimitOneMillionMiddleware::check(implode('|', $this->scopes['scopes']), 'restitusi');
			}
			RequiredPasswordMiddleware::check();
			
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
		$today		= Carbon::addyears(1);
		$tomorrow	= Carbon::addyears(1)->adddays(1);
		//1. PANEL ANGSURAN

		//a. STAT SISA HUTANG
		$stat['sisa_hutang']		= Calculator::hutangBefore($aktif['nomor_kredit'], $tomorrow);

		//b. STAT ANGSURAN JT
		$stat['angsuran_bulanan']	= JadwalAngsuran::where('nomor_kredit', $aktif['nomor_kredit'])->orderby('nth', 'asc')->wherenull('nomor_faktur')->select('jumlah as total')->first()['total'];
		$stat['total_tunggakan']	= Calculator::piutangBefore($aktif['nomor_kredit'], $tomorrow);
		$stat['jumlah_tunggakan']	= floor($stat['total_tunggakan']/max(1, $stat['angsuran_bulanan']));

		//c. STAT TITIPAN
		$stat['total_titipan']		= Calculator::titipanBefore($aktif['nomor_kredit'], $tomorrow);
		$stat['jumlah_titipan']		= floor($stat['total_titipan']/max(1, $stat['angsuran_bulanan']));

		//d. Bukti Transaksi
		$notabayar 		= NotaBayar::where('morph_reference_id', $aktif['nomor_kredit'])->where('morph_reference_tag', 'kredit')->whereIn('jenis', ['angsuran', 'kolektor'])->get();

		//e. NTH Angsuran
		$sisa_angsuran 	= JadwalAngsuran::wherenull('nomor_faktur')->where('nomor_kredit', $aktif['nomor_kredit'])->selectraw('jumlah as total')->orderby('nth', 'asc')->get();

		//f. Bukti Transaksi kolektor
		$kas_kolektor 	= NotaBayar::where('morph_reference_id', $aktif['nomor_kredit'])->where('morph_reference_tag', 'kredit')->whereIn('jenis', ['kolektor'])->wheredoesnthave('child', function($q){$q;})->get();

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
		$r3d 		= Calculator::restitusi3DBefore($aktif['nomor_kredit'], $today);

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
		}elseif(in_array('tagihan', $this->scopes['scopes'])){
			view()->share('is_penagihan_tab', 'show active');
		}elseif(in_array('restitusi', $this->scopes['scopes'])){
			view()->share('is_denda_tab', 'show active');
			view()->share('is_restitusi_tab', 'show active');
		}else{
			view()->share('is_angsuran_tab', 'show active');
		}

		$bayar 		= null;
		if(request()->has('bayar')){

			$tanggal 	= Carbon::createfromformat('d/m/Y', request()->get('tanggal'));
			$bayar 	= new PerhitunganBayar($aktif['nomor_kredit'], $tanggal, request()->get('jumlah_angsuran'), request()->get('nominal'));

			if(str_is($aktif['jenis_pinjaman'], 'pa')){
				$bayar 	= $bayar->pa();
			}elseif(str_is($aktif['jenis_pinjaman'], 'pt')){
				$bayar 	= $bayar->pt();
			}

			$faktur	= PerhitunganBayar::generateFaktur($aktif['nomor_kredit'], $bayar, $tanggal);
		}

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		view()->share('akun', $akun);
		view()->share('aktif', $aktif);
		view()->share('angsuran', $angsuran);
		view()->share('sisa_angsuran', $sisa_angsuran);
		view()->share('notabayar', $notabayar);
		view()->share('kas_kolektor', $kas_kolektor);
		view()->share('titipan', $titipan);
		view()->share('jaminan', $jaminan);
		view()->share('suratperingatan', $suratperingatan);
		view()->share('denda', $denda);
		view()->share('restitusi', $restitusi);
		view()->share('stat', $stat);
		view()->share('r3d', $r3d);
		view()->share('bayar', $bayar);
		view()->share('faktur', $faktur);

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
				case 'kolektor':
					$current = 'tagihan';
					$this->middleware_store_tagihan($aktif);
					break;
				case 'penerimaan_kas_kolektor':
					$current = 'angsuran';
					$this->middleware_penerimaan_kas_kolektor($aktif);
					break;
				case 'angsuran':
					$current = 'angsuran';
					$this->middleware_store_angsuran($aktif);
					break;
				case 'permintaan_restitusi':
					$current = 'denda';
					$this->middleware_store_permintaan_restitusi($aktif);
					break;
				case 'validasi_restitusi':
					$current = 'denda';
					$this->middleware_store_validasi_restitusi($aktif);
					break;
				case 'denda':
					$current = 'denda';
					$this->middleware_store_denda($aktif);
					break;
			}
			DB::commit();
			return redirect()->route('kredit.show', ['id' => $id, 'current' => $current, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]);
		} catch (Exception $e) {
			DB::rollback();
			return redirect()->route('kredit.show', ['id' => $id, 'current' => $current, 'kantor_aktif_id' => request()->get('kantor_aktif_id')])->withErrors($e->getMessage());
		}
	}

	public function store(){
		return $this->update(request()->get('id'));
	}
}
