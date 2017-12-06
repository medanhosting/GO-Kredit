<?php

namespace App\Http\Controllers\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\Penagihan;
use Carbon\Carbon, Exception, DB, Config, Auth;

use App\Service\Traits\IDRTrait;

class PenagihanController extends Controller
{
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();

		$this->middleware('required_password')->only('store');
	}
	public function index () 
	{
		$today 		= Carbon::now();

		$tunggakan 	= AngsuranDetail::LihatJatuhTempo($today)->wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));});

		if(request()->has('q')){
			$look 		= '%'.request()->get('q').'%';
			$tunggakan 	= $tunggakan->where(function($q)use($look){$q->where('nomor_kredit', 'like', $look)->orwherehas('kredit', function($q2)use($look){$q2->where('nasabah->nama', 'like', $look);});});
		}

		$tunggakan 	= $tunggakan->hitungtunggakan()->with(['kredit', 'kredit.penagihan'])->paginate();

		view()->share('tunggakan', $tunggakan);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.penagihan.index');
		return $this->layout;
	}

	public function show($id) 
	{
		$today 		= Carbon::now()->subDays(Config::get('kredit.batas_pembayaran_angsuran_hari'));

		$tunggakan 	= AngsuranDetail::lihatJatuhTempo($today)->displaying()->where('nomor_kredit', $id)->wherenull('nota_bayar_id')->get()->toArray();

		$penagihan 	= Penagihan::where('nomor_kredit', $id)->orderby('tanggal', 'desc')->get();

		$can_collect= 1;

		if($penagihan->count()){
			$latest = $today->diffInDays(Carbon::createFromFormat('d/m/Y H:i', $penagihan[0]['tanggal']));
			if($latest < Config::get('kredit.selisih_penagihan_hari')){
				$can_collect 	= 0;
			}
		}
		
		$total		= array_sum(array_column($tunggakan, 'subtotal'));

		view()->share('nomor_kredit', $id);
		view()->share('total', $total);
		view()->share('tunggakan', $tunggakan);
		view()->share('penagihan', $penagihan);
		view()->share('can_collect', $can_collect);
		view()->share('today', $today);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.penagihan.show');
		return $this->layout;
	}

	public function store(){
		try {
			$today 		= Carbon::now()->subDays(Config::get('kredit.batas_pembayaran_angsuran_hari'));
		
			$tunggakan 	= AngsuranDetail::lihatJatuhTempo($today)->where('nomor_kredit', request()->get('nomor_kredit'))->wherenull('nota_bayar_id')->wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->first();

			if(!$tunggakan){
				throw new Exception("Tidak ada tunggakan untuk tagihan ini", 1);
			}

			DB::beginTransaction();

			$penagihan 	= new Penagihan;
			$penagihan->nomor_kredit 	= $tunggakan['nomor_kredit'];
			$penagihan->tanggal 		= request()->get('tanggal');
			$penagihan->nip_karyawan 	= Auth::user()['nip'];
			$penagihan->save();
	
			DB::commit();
			return redirect()->route('kredit.penagihan.show', ['id' => $tunggakan['nomor_kredit'], 'kantor_aktif_id' => request()->get('kantor_aktif_id')]);
		} catch (Exception $e) {
			DB::rollback();
			return redirect()->back()->withErrors($e->getMessage());
		}
	}
}
