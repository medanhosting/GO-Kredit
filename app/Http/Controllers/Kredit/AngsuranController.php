<?php

namespace App\Http\Controllers\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Angsuran;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use App\Http\Service\Policy\PelunasanAngsuran;
use Carbon\Carbon, Exception, DB, Config;

use App\Service\Traits\IDRTrait;

class AngsuranController extends Controller
{
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();

		$this->middleware('required_password')->only('update');
	}

	public function index () 
	{
		$today 		= Carbon::now()->addDays(15);

		$angsuran 	= Angsuran::lihatJatuhTempo($today)->countAmount()->where('kode_kantor', request()->get('kantor_aktif_id'))->paginate();

		view()->share('angsuran', $angsuran);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.angsuran.index');
		return $this->layout;
	}

	public function show($id) {
		$today 		= Carbon::now()->addDays(15);

		$angsuran 	= Angsuran::countAmount()->where('kode_kantor', request()->get('kantor_aktif_id'))->where('k_angsuran.id', $id)->with(['details'])->first();

		$get_id 	= Angsuran::where('nomor_kredit', $angsuran->nomor_kredit)->get(['id']);
		$ids 		= array_column($get_id->toArray(), 'id');

		$t_hutang 	= Angsuran::hitungTotalHutang($angsuran->nomor_kredit);
		$t_lunas 	= Angsuran::hitungHutangDibayar($angsuran->nomor_kredit);
		$s_hutang 	= $t_hutang - $t_lunas;

		if(request()->has('pelunasan') && request()->get('pelunasan')){
			$lunas 	= $this->formatMoneyFrom(PelunasanAngsuran::hitung($angsuran['nomor_kredit'], $angsuran['id']));
			view()->share('lunas', $lunas);

			$t_lunas 	= $t_lunas + $lunas;
			$s_hutang 	= $t_hutang - $t_lunas;
		}

		view()->share('t_hutang', $this->formatMoneyTo($t_hutang));
		view()->share('s_hutang', $this->formatMoneyTo($s_hutang));
		view()->share('t_lunas', $this->formatMoneyTo($t_lunas));
		view()->share('angsuran', $angsuran);
		view()->share('today', $today);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.angsuran.show');
		return $this->layout;
	}

	public function update($id){
		try {

			$angsuran 	= Angsuran::where('kode_kantor', request()->get('kantor_aktif_id'))->where('id', $id)->first();

			if(!$angsuran){
				throw new Exception("Data angsuran tidak ada", 1);
			}

			DB::BeginTransaction();
			if(request()->has('pelunasan') && request()->get('pelunasan')){
				//create new angsuran
				$lunas 	= PelunasanAngsuran::hitung($angsuran['nomor_kredit'], $angsuran['id']);
				$a_d 	= new AngsuranDetail;
				$a_d->angsuran_id 	= $id;
				$a_d->tag 			= 'pelunasan';
				$a_d->amount 		= $lunas;
				$a_d->description 	= 'Pelunasan Angsuran';
				$a_d->save();
			}
		
			$angsuran->paid_at 	= Carbon::now()->format('d/m/Y H:i');
			$angsuran->save();

			DB::commit();

			return redirect()->route('kredit.angsuran.show', ['id' => $id, 'kantor_aktif_id' => $angsuran['kode_kantor']]);
		} catch (Exception $e) {
			DB::rollback();
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function print($id) {
		try {
			$angsuran 	= Angsuran::countAmount()->wherenotnull('paid_at')->where('kode_kantor', request()->get('kantor_aktif_id'))->where('k_angsuran.id', $id)->with(['details'])->first();

			if(!$angsuran){
				throw new Exception("Data angsuran tidak ada", 1);
			}

			$t_hutang 	= Angsuran::hitungTotalHutang($angsuran->nomor_kredit);
			$t_lunas 	= Angsuran::hitungHutangDibayar($angsuran->nomor_kredit);
			$s_hutang 	= $t_hutang - $t_lunas;

			view()->share('t_hutang', $this->formatMoneyTo($t_hutang));
			view()->share('s_hutang', $this->formatMoneyTo($s_hutang));
			view()->share('t_lunas', $this->formatMoneyTo($t_lunas));

			return view('kredit.angsuran.print', compact('angsuran'));
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}
}
