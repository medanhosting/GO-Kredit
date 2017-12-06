<?php

namespace App\Http\Controllers\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Carbon\Carbon, Exception, DB, Config, Auth;

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
		$today 		= Carbon::now();

		$angsuran 	= AngsuranDetail::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));});

		if(request()->has('q')){
			$look 		= '%'.request()->get('q').'%';
			$angsuran 	= $angsuran->where(function($q)use($look){$q->where('nomor_kredit', 'like', $look)->orwherehas('kredit', function($q2)use($look){$q2->where('nasabah->nama', 'like', $look);});});
		}

		$angsuran 	= $angsuran->selectraw('nomor_kredit')->selectraw('sum(amount) as total_hutang')->selectraw(\DB::raw('SUM(IFNULL(nota_bayar_id,amount)) as sisa_angsuran'))->groupby('nomor_kredit')->with(['kredit'])->paginate();

		view()->share('angsuran', $angsuran);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.angsuran.index');
		return $this->layout;
	}

	public function show($id) {
		$today 		= Carbon::now();

		if(request()->has('nota_bayar_id')){
			$angsuran 			= NotaBayar::where('id', request()->get('nota_bayar_id'))->where('nomor_kredit', $id)->firstorfail();
		}else{
			$angsuran['nomor_faktur']	= NotaBayar::generatenomorfaktur($id);
		}

		$angsuran['kredit']		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();

		$angsuran['details'] 	= AngsuranDetail::displaying()->where('nomor_kredit', $id);

		if(request()->has('nth')){
			$angsuran['details']	= $angsuran['details']->whereIn('nth', request()->get('nth'));
			view()->share('bayar', true);
		}
		if(request()->has('nota_bayar_id')){
			$angsuran['details']	= $angsuran['details']->where('nota_bayar_id', request()->get('nota_bayar_id'));
			view()->share('lunas', true);
		}
		
		$angsuran['details'] 	= $angsuran['details']->get()->toArray();

		$total		= array_sum(array_column($angsuran['details'], 'subtotal'));
		$t_hutang 	= (string)AngsuranDetail::hitungTotalHutang($id) * 1;
		$t_lunas 	= $total + ((string)AngsuranDetail::hitungHutangDibayar($id, $angsuran['id']) * 1);
		// $t_lunas 	= (string)AngsuranDetail::hitungHutangDibayar($id) * 1;
		$s_hutang 	= $t_hutang - $t_lunas;


		view()->share('t_hutang', $this->formatMoneyTo($t_hutang));
		view()->share('s_hutang', $this->formatMoneyTo($s_hutang));
		view()->share('t_lunas', $this->formatMoneyTo($t_lunas));
		view()->share('angsuran', $angsuran);
		view()->share('today', $today);
		view()->share('total', $total);
		view()->share('id', $id);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.angsuran.show');
		return $this->layout;
	}

	public function update($id){
		try {

			$kredit		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();

			if(!$kredit){
				throw new Exception("Data angsuran tidak ada", 1);
			}

			$angsuran 	= AngsuranDetail::where('nomor_kredit', $id)->whereIn('nth', request()->get('nth'))->get();

			DB::BeginTransaction();

			$paid_at 	= new NotaBayar;
			$paid_at->nomor_kredit 	= $id;
			$paid_at->tanggal 		= Carbon::now()->format('d/m/Y H:i');
			$paid_at->nip_karyawan 	= Auth::user()['nip'];
			$paid_at->nomor_faktur 	= NotaBayar::generatenomorfaktur($id);
			$paid_at->save();

			foreach ($angsuran as $k => $v) {
				$v->nota_bayar_id 	= $paid_at->id;
				$v->save();
			}

			DB::commit();

			return redirect()->route('kredit.angsuran.show', ['id' => $id, 'kantor_aktif_id' => $kredit['kode_kantor'], 'nota_bayar_id' => $paid_at->id]);
		} catch (Exception $e) {
			DB::rollback();
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function print($id) {
		try {
			$angsuran				= NotaBayar::where('nomor_faktur', $id)->wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->with(['kredit'])->first()->toArray();

			$angsuran['details'] 	= AngsuranDetail::displaying()->where('nomor_kredit', $angsuran['nomor_kredit'])->where('nota_bayar_id', $angsuran['id'])->get()->toArray();

			$total		= array_sum(array_column($angsuran['details'], 'subtotal'));

			$t_hutang 	= (string)AngsuranDetail::hitungTotalHutang($angsuran['nomor_kredit']) * 1;
			$t_lunas 	= $total + ((string)AngsuranDetail::hitungHutangDibayar($angsuran['nomor_kredit'], $angsuran['id']) * 1);
			$s_hutang 	= $t_hutang - $t_lunas;

			view()->share('total', $total);
			view()->share('t_hutang', $this->formatMoneyTo($t_hutang));
			view()->share('s_hutang', $this->formatMoneyTo($s_hutang));
			view()->share('t_lunas', $this->formatMoneyTo($t_lunas));

			return view('kredit.angsuran.print', compact('angsuran'));
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}
}
