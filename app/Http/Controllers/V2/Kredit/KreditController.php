<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\SuratPeringatan;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\MutasiJaminan;

use Exception, DB, Auth, Carbon\Carbon, Config;

use App\Service\Traits\IDRTrait;

use App\Http\Controllers\V2\Traits\AkunTrait;
use App\Http\Controllers\V2\Traits\KreditTrait;

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
		$stat['sisa_hutang']		= AngsuranDetail::whereIn('tag', ['pokok', 'bunga'])->wherenull('nota_bayar_id')->where('nomor_kredit', $aktif['nomor_kredit'])->sum('amount');
		$stat['total_titipan']		= AngsuranDetail::whereIn('tag', ['titipan', 'pengambilan_titipan'])->where('nomor_kredit', $aktif['nomor_kredit'])->where('tanggal', '<=', $today->format('Y-m-d H:i:s'))->sum('amount');
		$stat['total_denda']		= AngsuranDetail::whereIn('tag', ['denda', 'restitusi_denda'])->wherenull('nota_bayar_id')->where('nomor_kredit', $aktif['nomor_kredit'])->sum('amount');

		//DATA ANGSURAN
		$angsuran 	= AngsuranDetail::displaying()->where('nomor_kredit', $aktif['nomor_kredit'])->get();
	
		//DATA DENDA
		$denda 		= AngsuranDetail::displayingdenda()->where('nomor_kredit', $aktif['nomor_kredit'])->get();
		
		//PANEL TUNGGAKAN/PENAGIHAN
		$stat['last_pay'] 			= NotaBayar::where('nomor_kredit', $aktif['nomor_kredit'])->orderby('tanggal', 'desc')->first();
		$stat['total_tunggakan']	= AngsuranDetail::whereIn('tag', ['pokok', 'bunga'])->wherenull('nota_bayar_id')->where('nomor_kredit', $aktif['nomor_kredit'])->where('tanggal', '<=', $today->format('Y-m-d H:i:s'))->sum('amount');
		$stat['total_hutang']		= AngsuranDetail::whereIn('tag', ['pokok', 'bunga'])->where('nomor_kredit', $aktif['nomor_kredit'])->sum('amount');

		$stat['last_sp'] 			= SuratPeringatan::where('nomor_kredit', $aktif['nomor_kredit'])->orderby('tanggal', 'desc')->orderby('created_at', 'desc')->first();

		//DATA SP
		$suratperingatan 	= SuratPeringatan::where('nomor_kredit', $aktif['nomor_kredit'])->with(['penagihan', 'penagihan.notabayar'])->get();

		//PANEL JAMINAN
		$ids 		= MutasiJaminan::where('nomor_kredit', $aktif['nomor_kredit'])->selectraw('max(id) as id, max(tanggal) as tanggal')->groupby('nomor_jaminan')->orderby('tanggal', 'desc')->get()->toArray();
		$jaminan 	= MutasiJaminan::wherein('id', array_column($ids, 'id'))->get();

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
		view()->share('jaminan', $jaminan);
		view()->share('suratperingatan', $suratperingatan);
		view()->share('denda', $denda);
		view()->share('stat', $stat);

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
