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
		$angsuran 	= NotaBayar::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));});

		if(request()->has('start')){
			$start		= Carbon::createFromFormat('d/m/Y', request()->get('start'))->startofday();
			$angsuran 	= $angsuran->where('tanggal', '>=', $start->format('Y-m-d H:i:s'));
		}
		if(request()->has('end')){
			$end		= Carbon::createFromFormat('d/m/Y', request()->get('end'))->endofday();
			$angsuran 	= $angsuran->where('tanggal', '<=', $end->format('Y-m-d H:i:s'));
		}

		$angsuran 	= $angsuran->wherehas('details', function($q){$q;})->Displaying()->paginate();

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

		view()->share('active_submenu', 'kredit');
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

			return view('v2.print.angsuran.bukti_angsuran');
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function potongan($id){
		$aktif		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();
		$potongan 	= PelunasanAngsuran::potongan($aktif['nomor_kredit']);

		return response()->json(['message' => 'success', 'data' => $potongan], 200);
	}

	public function tagihan($id){
		
		$aktif		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();

		$nth 		= array_flatten(request()->get('nth'));
		
		$angsuran 	= AngsuranDetail::displaying()->where('nomor_kredit', $aktif['nomor_kredit']);

		if(is_array($nth)){
			$angsuran 	= $angsuran->wherein('nth', $nth);
		}else{
			$angsuran 	= $angsuran->where('nth', $nth);
		}

		$angsuran 	= $angsuran->get();

		return response()->json(['message' => 'success', 'data' => $angsuran], 200);
	}

	public function denda($id) 
	{
		$aktif		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();

		$denda 		= AngsuranDetail::displayingdenda()->where('nomor_kredit', $aktif['nomor_kredit'])->wherenull('nota_bayar_id')->get();
		$t_denda 	= AngsuranDetail::whereIn('tag', ['denda', 'restitusi_denda'])->where('nomor_kredit', $aktif['nomor_kredit'])->wherenull('nota_bayar_id')->sum('amount');

		return response()->json(['status' => 'success', 'data' => $denda], 200);
	}

	public function titipan($id) 
	{
		$titipan 	= AngsuranDetail::where('nomor_kredit', $id)->where('tag', ['titipan', 'pengambilan_titipan'])->sum('amount');

		return response()->json(['status' => 'success', 'data' => $titipan], 200);
	}
}
