<?php

namespace App\Http\Controllers\V2\Finance;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Exception, DB, Auth, Carbon\Carbon;

use Thunderlabid\Finance\Models\COA;
use Thunderlabid\Finance\Models\Account;
use Thunderlabid\Finance\Models\TransactionDetail;

class JurnalController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->middleware('scope:kasir');
	}

	public function index () 
	{
		$akun 		= Account::wherenull('akun_id')->where('kode_kantor', request()->get('kantor_aktif_id'))->with(['subakun', 'subakun.detailsin', 'subakun.detailsout'])->get();

		view()->share('active_submenu', 'jurnal');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.jurnal.index', compact('akun'));
		return $this->layout;
	}
}