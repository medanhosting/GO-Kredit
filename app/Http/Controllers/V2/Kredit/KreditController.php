<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\SuratPeringatan;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\MutasiJaminan;

use Exception, DB, Auth, Carbon\Carbon;

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
		$aktif 		= Aktif::kantor(request()->get('kantor_aktif_id'))->PembayaranBerikut()->with(['jaminan'])->paginate(15, ['*'], 'aktif');

		view()->share('is_aktif_tab', 'show active');

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.index', compact('aktif'));
		return $this->layout;
	}

	public function show($id) 
	{
		$aktif 		= Aktif::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->PembayaranBerikut()->first();

		$akun 		= $this->get_akun(request()->get('kantor_aktif_id'));

		//ANGSURAN
		$angsuran 	= AngsuranDetail::displaying()->where('nomor_kredit', $aktif['nomor_kredit'])->get();

		$denda 		= AngsuranDetail::displayingdenda()->where('nomor_kredit', $aktif['nomor_kredit'])->get();

		$t_denda 	= AngsuranDetail::whereIn('tag', ['denda', 'potongan_denda'])->where('nomor_kredit', $aktif['nomor_kredit'])->sum('amount');

		$titipan 	= NotaBayar::wheredoesnthave('details',  function($q){$q;})->where('nomor_kredit', $aktif['nomor_kredit'])->sum('jumlah');

		$total		= array_sum(array_column($angsuran->toArray(), 'subtotal'));

		//CHECK TITIPAN
		$titipan 	= NotaBayar::where('nomor_kredit', $aktif['nomor_kredit'])->wheredoesnthave('details', function($q){$q;})->sum('jumlah');
		
		//TUNGGAKAN
		$today		= Carbon::now();
		// $dday		= Carbon::createFromFormat('d/m/Y H:i', $aktif['tanggal']);
		$tunggakan  = AngsuranDetail::where('nomor_kredit', $aktif['nomor_kredit'])->wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($today)->orderby('tanggal', 'asc')->first();
		//CHECK SP YANG BELUM DIKIRIM
		$sp 		= SuratPeringatan::wheredoesnthave('penagihan', function($q){$q;})->first();

		$riwayat_t  = AngsuranDetail::where('nomor_kredit', $aktif['nomor_kredit'])->wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($today)->orderby('tanggal', 'asc')->get();

		$latest_pay = NotaBayar::where('nomor_kredit', $aktif['nomor_kredit'])->orderby('tanggal', 'desc')->first();

		$penagihan 	= Penagihan::wherehas('kredit', function($q)use($id){$q->where('kode_kantor', request()->get('kantor_aktif_id'))->where('id', $id);})->HitungNotaBayar()->orderby('tanggal', 'asc')->get();

		$jaminan 	= MutasiJaminan::where('nomor_kredit', $aktif['nomor_kredit'])->get();

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

		view()->share('aktif', $aktif);
		view()->share('angsuran', $angsuran);
		view()->share('total', $total);
		view()->share('tunggakan', $tunggakan);
		view()->share('latest_pay', $latest_pay);
		view()->share('riwayat_t', $riwayat_t);
		view()->share('penagihan', $penagihan);
		view()->share('jaminan', $jaminan);

		view()->share('kredit_id', $id);

		$this->layout->pages 	= view('v2.kredit.show', compact('sp', 'denda', 't_denda', 'titipan', 'akun'));
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
