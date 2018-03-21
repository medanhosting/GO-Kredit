<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Kredit\Models\PermintaanRestitusi;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;

use App\Service\Traits\IDRTrait;
use App\Service\System\Calculator;

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
		$angsuran 				= NotaBayar::where('id', request()->get('nota_bayar_id'))->where('nomor_kredit', $id)->firstorfail();
		$tanggal_bayar 			= Carbon::createFromFormat('d/m/Y H:i', $angsuran->tanggal);

		$angsuran['kredit']		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();

		$case = 'angsuran';
		if(request()->has('case')){
			$case = request()->get('case');
		}

		if(str_is($case, 'angsuran')){
			$angsuran['details']= JadwalAngsuran::displaying()->where('nomor_kredit', $id);
		}else{
			$angsuran['details']= JadwalAngsuran::displayingdenda()->where('nomor_kredit', $id);
		}

		$angsuran['details']	= $angsuran['details']->where('nota_bayar_id', request()->get('nota_bayar_id'));
		$angsuran['details'] 	= $angsuran['details']->get()->toArray();

		$sisa_angsuran			= JadwalAngsuran::where('nomor_kredit', $id)->whereIn('tag', ['pokok', 'bunga'])->where(function($q)use($tanggal_bayar){$q->wherehas('notabayar', function($q)use($tanggal_bayar){$q->where('tanggal' , '>', $tanggal_bayar->format('Y-m-d H:i:s'));})->orwherenull('nota_bayar_id');})->sum('amount');
		
		view()->share('angsuran', $angsuran);
		view()->share('tanggal_bayar', $tanggal_bayar);
		view()->share('sisa_angsuran', $sisa_angsuran);
		view()->share('id', $id);

		view()->share('active_submenu', 'angsuran');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.show.angsuran.bukti_pembayaran_'.$case);
		return $this->layout;
	}

	public function print ($id)
	{
		try {
			$angsuran 				= NotaBayar::where('nomor_faktur', request()->get('nomor_faktur'))->where('morph_reference_id', $id)->whereIn('morph_reference_tag', ['kredit', 'finance'])->selectraw('*')->selectraw('jumlah as total')->selectraw('abs(jumlah) as abstotal')->firstorfail();

			$tanggal_bayar 			= Carbon::createFromFormat('d/m/Y H:i', $angsuran->tanggal);
			$tanggal_bayar_besok 	= Carbon::createFromFormat('d/m/Y H:i', $angsuran->tanggal)->adddays(1);

			if(str_is($angsuran['morph_reference_tag'], 'kredit')){
				$angsuran['kredit']		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();
			}else{
				$angsuran['kredit']		= Aktif::where('nomor_kredit', $angsuran['parent']['morph_reference_id'])->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();
			}
			$case = 'angsuran';
			if(request()->has('case')){
				$case = request()->get('case');
			}

			if(str_is($case, 'angsuran')){
				$angsuran['details']= DetailTransaksi::where('nomor_faktur', $angsuran['nomor_faktur']);
			}elseif(str_is($case, 'sementara')){
				$angsuran['details']= DetailTransaksi::where('nomor_faktur', $angsuran['nomor_faktur']);
			}elseif(str_is($case, 'tukar_sementara')){
				$case = 'angsuran';
				$angsuran['details']= DetailTransaksi::where('nomor_faktur', $angsuran['nomor_faktur']);
			}elseif(str_is($case, 'restitusi_denda')){
				$angsuran['restitusi']	= PermintaanRestitusi::where('nomor_faktur', $angsuran['nomor_faktur'])->first();
				$angsuran['denda'] 	= Calculator::dendaBefore($id, $tanggal_bayar_besok->startofday());
				$angsuran['details']= DetailTransaksi::where('nomor_faktur', $angsuran['nomor_faktur']);
			}else{
				$angsuran['details']= DetailTransaksi::where('nomor_faktur', $angsuran['nomor_faktur']);
			}
			$angsuran['details'] 	= $angsuran['details']->selectraw('*')->selectraw('jumlah as total')->selectraw('abs(jumlah) as abstotal')->get()->toArray();

			$nth 		= implode(', ', array_column(JadwalAngsuran::where('nomor_faktur', $angsuran['nomor_faktur'])->get(['nth'])->toArray(), 'nth'));
			$potongan 	= array_sum(array_column($angsuran['details'], 'total')) - $angsuran['total'];

			$sisa_angsuran		= Calculator::hutangExactlyBefore($angsuran['kredit']['nomor_kredit'], $tanggal_bayar);
			$titipan_saat_itu	= Calculator::titipanExactlyBefore($angsuran['kredit']['nomor_kredit'], $tanggal_bayar);

			view()->share('angsuran', $angsuran);
			view()->share('tanggal_bayar', $tanggal_bayar);
			view()->share('sisa_angsuran', $sisa_angsuran);
			view()->share('id', $id);
			view()->share('nth', $nth);
			view()->share('potongan', $potongan);
			view()->share('titipan_saat_itu', $titipan_saat_itu);
			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

			return view('v2.kredit.print.bukti_pembayaran_'.$case);
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
		
		return response()->json(request()->all());
		$aktif		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();

		$nth 		= array_flatten(request()->get('nth'));
		
		$angsuran 	= JadwalAngsuran::where('nomor_kredit', $aktif['nomor_kredit']);

		if(is_array($nth)){
			$angsuran 	= $angsuran->wherein('nth', $nth);
		}else{
			$angsuran 	= $angsuran->where('nth', $nth);
		}

		$angsuran 	= $angsuran->get(['nth', 'jumlah as subtotal']);

		return response()->json(['message' => 'success', 'data' => $angsuran], 200);
	}

	public function denda($id) 
	{
		$aktif		= Aktif::where('nomor_kredit', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->firstorfail();

		$denda 		= JadwalAngsuran::displayingdenda()->where('nomor_kredit', $aktif['nomor_kredit'])->wherenull('nota_bayar_id')->get();
		$t_denda 	= JadwalAngsuran::whereIn('tag', ['denda', 'restitusi_denda'])->where('nomor_kredit', $aktif['nomor_kredit'])->wherenull('nota_bayar_id')->sum('amount');

		return response()->json(['status' => 'success', 'data' => $denda], 200);
	}

	public function titipan($id) 
	{
		$titipan	= DetailTransaksi::whereIn('tag', ['titipan_pokok', 'titipan_bunga', 'restitusi_titipan_pokok', 'restitusi_titipan_bunga'])->wherehas('notabayar', function($q)use($id){$q->where('morph_reference_id', $id)->where('morph_reference_tag', 'kredit');})->sum('jumlah');

		return response()->json(['status' => 'success', 'data' => $titipan], 200);
	}
}
