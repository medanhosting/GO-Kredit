<?php

namespace App\Http\Controllers\V2\Finance;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Exception, DB, Auth, Carbon\Carbon;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Finance\Models\Account;
use Thunderlabid\Finance\Models\Jurnal;
use Thunderlabid\Finance\Models\DetailTransaksi;

class KasirController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->middleware('scope:'.implode('|', $this->acl_menu['keuangan.lkh']))->only(['lkh']);
	}

	public function lkh() 
	{
		$dbefore 	= Carbon::parse('yesterday')->endofDay();
		$dday 		= Carbon::now()->startofday()->addhours(15);

		if(request()->has('q')){
			$dbefore 	= Carbon::createFromFormat('d/m/Y', request()->get('q'))->subdays(1)->endofDay();
			$dday 		= Carbon::createFromFormat('d/m/Y', request()->get('q'))->startofday()->addhours(15);
		}

		$balance 	= Jurnal::wherehas('coa', function($q){$q->where('nomor_perkiraan', 'like', '100.%')->wherenotnull('f_coa.coa_id')->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('tanggal', '<=', $dbefore->format('Y-m-d H:i:s'))->sum('jumlah');
		$out 		= Jurnal::wherehas('coa', function($q){$q->where('nomor_perkiraan', 'like', '100.%')->wherenotnull('f_coa.coa_id')->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('tanggal', '>', $dbefore->format('Y-m-d H:i:s'))->where('tanggal', '<=', $dday->format('Y-m-d H:i:s'))->where('jumlah', '<=', 0)->sum('jumlah');
		$in 		= Jurnal::wherehas('coa', function($q){$q->where('nomor_perkiraan', 'like', '100.%')->wherenotnull('f_coa.coa_id')->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('tanggal', '>', $dbefore->format('Y-m-d H:i:s'))->where('tanggal', '<=', $dday->format('Y-m-d H:i:s'))->where('jumlah', '>=', 0)->sum('jumlah');

		view()->share('active_submenu', 'kasir');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kasir.index', compact('balance', 'in', 'out', 'dbefore', 'dday'));
		return $this->layout;
	}

	public function jurnalpagi() 
	{
		$today 	= Carbon::now();
		if(request()->has('q')){
			$today 	= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}
		$sday 	= $today->startofday()->format('Y-m-d H:i:s');
		$eday 	= $today->startofday()->format('Y-m-d H:i:s');

		$kredit 	= Aktif::buatJurnalPagi($sday, $eday)->paginate();

		view()->share('active_submenu', 'jurnalpagi');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kasir.jurnalpagi', compact('kredit'));
		return $this->layout;
	}

	public function print ()
	{
		$dbefore 	= Carbon::parse('yesterday')->endofDay();
		$dday 		= Carbon::now()->startofday()->addhours(15);

		if (request()->has('q'))
		{
			$dbefore 	= Carbon::createFromFormat('d/m/Y', request()->get('q'))->subdays(1)->endofDay();
			$dday 		= Carbon::createFromFormat('d/m/Y', request()->get('q'))->startofday()->addhours(15);
		}

		$balance 	= TransactionDetail::wherehas('account', function($q) {
							$q->where('nomor_perkiraan', 'like', '100.%')
								->wherenotnull('f_account.akun_id')
								->where('kode_kantor', request()->get('kantor_aktif_id'));
						})->where('tanggal', '<=', $dbefore->format('Y-m-d H:i:s'))
						->sum('amount');

		$out 		= TransactionDetail::wherehas('account', function($q) {
							$q->where('nomor_perkiraan', 'like', '100.%')
								->wherenotnull('f_account.akun_id')
								->where('kode_kantor', request()->get('kantor_aktif_id'));
						})->where('tanggal', '>', $dbefore->format('Y-m-d H:i:s'))
						->where('tanggal', '<=', $dday->format('Y-m-d H:i:s'))
						->where('amount', '<=', 0)
						->sum('amount');

		$in 		= TransactionDetail::wherehas('account', function($q) {
							$q->where('nomor_perkiraan', 'like', '100.%')
								->wherenotnull('f_account.akun_id')
								->where('kode_kantor', request()->get('kantor_aktif_id'));
						})->where('tanggal', '>', $dbefore->format('Y-m-d H:i:s'))
						->where('tanggal', '<=', $dday->format('Y-m-d H:i:s'))
						->where('amount', '>=', 0)
						->sum('amount');	

		view()->share('balance', $balance);
		view()->share('in', $in);
		view()->share('out', $out);
		view()->share('dbefore', $dbefore);
		view()->share('dday', $dday);

		view()->share('html', ['title' => 'LAPORAN KASIR HARIAN']);

		return view('v2.print.finance.kasir.kas_harian');
	}
}