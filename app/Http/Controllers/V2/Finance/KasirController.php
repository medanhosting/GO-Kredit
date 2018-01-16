<?php

namespace App\Http\Controllers\V2\Finance;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Exception, DB, Auth, Carbon\Carbon;

use Thunderlabid\Finance\Models\Account;
use Thunderlabid\Finance\Models\TransactionDetail;

class KasirController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->middleware('scope:kasir');
	}

	public function index () 
	{
		$dbefore 	= Carbon::parse('yesterday')->endofDay();
		$dday 		= Carbon::now()->startofday()->addhours(15);

		if(request()->has('q')){
			$dbefore 	= Carbon::createFromFormat('d/m/Y', request()->get('q'))->subdays(1)->endofDay();
			$dday 		= Carbon::createFromFormat('d/m/Y', request()->get('q'))->startofday()->addhours(15);
		}

		$balance 	= TransactionDetail::wherehas('account', function($q){$q->where('nomor_perkiraan', 'like', '100.%')->wherenotnull('f_account.akun_id')->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('tanggal', '<=', $dbefore->format('Y-m-d H:i:s'))->sum('amount');

		$out 		= TransactionDetail::wherehas('account', function($q){$q->where('nomor_perkiraan', 'like', '100.%')->wherenotnull('f_account.akun_id')->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('tanggal', '>', $dbefore->format('Y-m-d H:i:s'))->where('tanggal', '<=', $dday->format('Y-m-d H:i:s'))->where('amount', '<=', 0)->sum('amount');
		$in 		= TransactionDetail::wherehas('account', function($q){$q->where('nomor_perkiraan', 'like', '100.%')->wherenotnull('f_account.akun_id')->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('tanggal', '>', $dbefore->format('Y-m-d H:i:s'))->where('tanggal', '<=', $dday->format('Y-m-d H:i:s'))->where('amount', '>=', 0)->sum('amount');

		view()->share('active_submenu', 'kasir');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kasir.index', compact('balance', 'in', 'out', 'dbefore', 'dday'));
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

		return view('v2.print.finance.kasir.kas_harian');
	}
}