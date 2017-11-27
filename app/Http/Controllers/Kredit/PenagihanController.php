<?php

namespace App\Http\Controllers\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\Angsuran;
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
		$today 		= Carbon::now()->subDays(Config::get('kredit.batas_pembayaran_angsuran_hari'));

		$tunggakan 	= Aktif::hitungtunggakan($today)->lihatJatuhTempo($today)->where('kode_kantor', request()->get('kantor_aktif_id'))->paginate();

		view()->share('tunggakan', $tunggakan);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.penagihan.index');
		return $this->layout;
	}

	public function show($id) 
	{
		$today 		= Carbon::now()->subDays(Config::get('kredit.batas_pembayaran_angsuran_hari'));

		$tunggakan 	= Angsuran::lihatJatuhTempo($today)->countAmount()->where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->wherenull('paid_at')->with(['details'])->get();

		$penagihan 	= Penagihan::where('nomor_kredit', $id)->orderby('collected_at', 'desc')->get();

		$can_collect= 1;

		if($penagihan->count()){
			$latest = $today->diffInDays(Carbon::createFromFormat('d/m/Y H:i', $penagihan[0]['collected_at']));
			if($latest < Config::get('kredit.selisih_penagihan_hari')){
				$can_collect 	= 0;
			}
		}

		view()->share('nomor_kredit', $id);
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
		
			$tunggakan 	= Aktif::hitungtunggakan($today)->lihatJatuhTempo($today)->where('nomor_kredit', request()->get('nomor_kredit'))->where('kode_kantor', request()->get('kantor_aktif_id'))->first();

			if(!$tunggakan){
				throw new Exception("Tidak ada tunggakan untuk tagihan ini", 1);
			}

			DB::beginTransaction();

			$penagihan 	= new Penagihan;
			$penagihan->nomor_kredit 	= $tunggakan['nomor_kredit'];
			$penagihan->collected_at 	= request()->get('collected_at');
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
