<?php

namespace App\Http\Controllers\V2\Finance;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;

use Thunderlabid\Finance\Models\Jurnal;
use Thunderlabid\Finance\Models\NotaBayar;

use App\Service\Traits\IDRTrait;

use Exception, DB, Auth, Carbon\Carbon;

class KasController extends Controller
{
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();
	}

	public function index() 
	{
		view()->share('active_submenu', 'kas');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kas.index');
		return $this->layout;
	}

	public function penerimaan($tipe = 'kas') 
	{
		$today 		= Carbon::now();

		if(request()->has('q')){
			$today 	= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		// titipan
		// pokok
		// bunga
		// bunga_dipercepat
		// denda
		// provisi
		// administrasi
		// legal
		$jurnal 	= NotaBayar::whereIn('jenis', ['angsuran', 'memorial_kolektor', 'denda', 'setoran_pencairan'])
		->join('f_detail_transaksi', 'f_detail_transaksi.nomor_faktur', 'f_nota_bayar.nomor_faktur')
		->join('f_jurnal', 'f_jurnal.detail_transaksi_id', 'f_detail_transaksi.id')
		->join('f_coa', 'f_jurnal.coa_id', 'f_coa.id')
		->where('f_nota_bayar.tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))
		->where('f_nota_bayar.tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))
		
		->selectraw('(sum(if(f_coa.nomor_perkiraan="200.210", f_jurnal.jumlah, 0)) * - 1) as titipan')

		->selectraw('(sum(if(f_coa.nomor_perkiraan="120.100", f_jurnal.jumlah, 0)) * - 1) as pokok_pa')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="120.200", f_jurnal.jumlah, 0)) * - 1) as pokok_pt')

		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.121", f_jurnal.jumlah, 0)) * - 1) as bunga_pa')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.122", f_jurnal.jumlah, 0)) * - 1) as bunga_pt')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.123", f_jurnal.jumlah, 0)) * - 1) as lain_lain')

		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.301", f_jurnal.jumlah, 0)) * - 1) as denda')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.201", f_jurnal.jumlah, 0)) * - 1) as provisi')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.202", f_jurnal.jumlah, 0)) * - 1) as administrasi')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.203", f_jurnal.jumlah, 0)) * - 1) as legal')
		->selectraw('f_nota_bayar.nomor_faktur')
		->selectraw('max(f_nota_bayar.tanggal) as tanggal')
		->selectraw('max(f_nota_bayar.morph_reference_id) as morph_reference_id')

		->where('f_jurnal.jumlah', '<', 0)
		->groupby('f_nota_bayar.nomor_faktur')
		->orderby('tanggal', 'asc')
		->get()
		;

		//total penerimaan
		$total 	= NotaBayar::whereIn('jenis', ['angsuran', 'memorial_kolektor', 'denda', 'setoran_pencairan'])
		->where('f_nota_bayar.tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))
		->where('f_nota_bayar.tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))
		->sum('jumlah');

		$p_total= NotaBayar::whereIn('jenis', ['angsuran', 'memorial_kolektor', 'denda', 'setoran_pencairan'])
		->where('f_nota_bayar.tanggal', '<', $today->startofday()->format('Y-m-d H:i:s'))
		->sum('jumlah');

		//JUMLAH ANGSURAN JATUH TEMPO	
		$total_jt 	= Jurnal::wherehas('coa', function($q){
			$q->whereIn('nomor_perkiraan', ['120.300','120.400','140.100','140.200']);
		})
		->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))
		->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))
		->where('jumlah', '<', 0)->sum('jumlah') * -1;

		$total_a 	= Jurnal::wherehas('coa', function($q){
			$q->whereIn('nomor_perkiraan', ['120.200','401.123']);
		})
		->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))
		->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))
		->where('jumlah', '<', 0)->sum('jumlah') * -1;

		view()->share('active_submenu', 'kasir');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kas.penerimaan', compact('jurnal', 'total', 'p_total', 'total_jt', 'total_a'));
		return $this->layout;
	}

	public function pengeluaran() 
	{
		$angsuran 	= NotaBayar::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('nomor_perkiraan', 'like', '100.%');
		$today 		= Carbon::now();

		if(request()->has('q')){
			$today		= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$angsuran 	= $angsuran->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'));

		$angsuran 		= $angsuran->wherehas('details', function($q){$q;})->where('jumlah', '<', 0)->get();

		$total_money 	= TransactionDetail::wherehas('account', function($q){$q->where('nomor_perkiraan', 'like', '100.%')->wherenotnull('f_account.akun_id')->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))->sum('amount');

		view()->share('active_submenu', 'kas_keluar');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kas.pengeluaran', compact('angsuran', 'total_money', 'today'));
		return $this->layout;
	}

	public function print($type)
	{
		$angsuran 	= NotaBayar::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('nomor_perkiraan', 'like', '100.%');

		$today 		= Carbon::now();

		if (request()->has('q'))
		{
			$today		= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$angsuran 	= $angsuran->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'));

		if ($type == 'penerimaan')
		{
			$angsuran 		= $angsuran->wherehas('details', function($q){$q;})->Displaying()->get();
			
			$total_money 		= TransactionDetail::wherehas('account', function($q) { 
										$q->where('nomor_perkiraan', 'like', '100.%')
											->wherenotnull('f_account.akun_id')
											->where('kode_kantor', request()->get('kantor_aktif_id'));
									})->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))
									->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))
									->sum('amount');

			$total_jatuh_tempo 	= NotaBayar::wherehas('details', function($q) {
										$q->whereIn('tag', ['pokok', 'bunga']);
									})->wheredoesnthave('details', function($q) {
										$q->whereIn('tag', ['potongan']);
									})->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))
									->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))
									->where('nomor_perkiraan', 'like', '100.%')
									->sum('jumlah');

			$total_pelunasan	= NotaBayar::wherehas('details', function($q) {
										$q->whereIn('tag', ['potongan']);
									})->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))
									->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))
									->where('nomor_perkiraan', 'like', '100.%')
									->sum('jumlah');

			$total_money_yesterday 	= TransactionDetail::wherehas('account', function($q) {
											$q->where('nomor_perkiraan', 'like', '100.%')
												->wherenotnull('f_account.akun_id')
												->where('kode_kantor', request()->get('kantor_aktif_id'));
										})->where('tanggal', '<', $today->startofday()->format('Y-m-d H:i:s'))
										->sum('amount');
			
			$total_angsuran 	= NotaBayar::wherehas('details', function($q) {
										$q->whereIn('tag', ['pokok', 'bunga']);
									})->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))
									->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))
									->where('nomor_perkiraan', 'like', '100.%')
									->sum('jumlah');
		} else {
			$angsuran 		= $angsuran->wherehas('details', function($q){$q;})->where('jumlah', '<', 0)->get();
			$total_money 	= TransactionDetail::wherehas('account', function($q) {
									$q->where('nomor_perkiraan', 'like', '100.%')
									->wherenotnull('f_account.akun_id')
									->where('kode_kantor', request()->get('kantor_aktif_id'));
								})->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))
								->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))
								->sum('amount');
		}

		view()->share('angsuran', $angsuran);
		view()->share('total_money', $total_money);
		view()->share('total_jatuh_tempo', $total_jatuh_tempo);
		view()->share('today', $today);
		view()->share('total_money_yesterday', $total_money_yesterday);
		view()->share('total_pelunasan', $total_pelunasan);
		view()->share('total_angsuran', $total_angsuran);

		if ($type == 'penerimaan') {
			return view('v2.print.finance.kas.penerimaan');
		} else {
			return view('v2.print.finance.kas.pengeluaran');
		}
	}
}
