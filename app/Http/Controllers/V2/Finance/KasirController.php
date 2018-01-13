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
		$akun	= Account::where('kode_kantor', request()->get('kantor_aktif_id'))->where('is_pasiva', false)->with(['coas', 'coas.detail'])->get();

		view()->share('active_submenu', 'kasir');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kasir.index', compact('akun'));
		return $this->layout;
	}
}