<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Service\Traits\IDRTrait;

use App\Http\Service\Policy\PelunasanAngsuran;

use Exception, DB, Auth, Carbon\Carbon;

class AngsuranController extends Controller
{
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();
	}

	public function index() 
	{
		$today 		= Carbon::now();

		if(request()->has('q')){
			$today	= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$angsuran 	= NotaBayar::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->wherehas('details', function($q){$q;})->Displaying()->paginate();

		view()->share('active_submenu', 'angsuran');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.angsuran.index', compact('angsuran'));
		return $this->layout;
	}

	public function show ($id)
	{
		$today 					= Carbon::now();
		$angsuran 				= NotaBayar::where('id', request()->get('nota_bayar_id'))->where('nomor_kredit', $id)->firstorfail();

		$angsuran['kredit']		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();

		$angsuran['details'] 	= AngsuranDetail::displaying()->where('nomor_kredit', $id);
		$angsuran['details']	= $angsuran['details']->where('nota_bayar_id', request()->get('nota_bayar_id'));
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

		$this->layout->pages 	= view('v2.kredit.show.angsuran.bukti_angsuran');
		return $this->layout;
	}

	public function print ($id)
	{
		try {
			$today 					= Carbon::now();
			$angsuran 				= NotaBayar::where('id', request()->get('nota_bayar_id'))->where('nomor_kredit', $id)->firstorfail();

			$angsuran['kredit']		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();

			$angsuran['details'] 	= AngsuranDetail::displaying()->where('nomor_kredit', $id);
			$angsuran['details']	= $angsuran['details']->where('nota_bayar_id', request()->get('nota_bayar_id'));
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

			return view('v2.print.angsuran.bukti_angsuran');;
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function potongan($id){
		$aktif		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();
		
		$potongan 	= PelunasanAngsuran::potongan($aktif['nomor_kredit']);
		return response()->json($potongan);
	}
}
