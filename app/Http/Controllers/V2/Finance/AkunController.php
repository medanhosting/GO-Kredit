<?php

namespace App\Http\Controllers\V2\Finance;

use App\Http\Controllers\Controller;

use Thunderlabid\Finance\Models\Account;

class AkunController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		// $this->middleware('scope:akun');
	}

	public function index () 
	{
		$aktiva	= Account::where('kode_kantor', request()->get('kantor_aktif_id'))->where('is_pasiva', false)->get();
		$pasiva	= Account::where('kode_kantor', request()->get('kantor_aktif_id'))->where('is_pasiva', true)->get();

		$counter = max(count($aktiva), count($pasiva));

		view()->share('active_submenu', 'akun');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.akun.index', compact('aktiva', 'pasiva', 'counter'));
		return $this->layout;
	}

	public function store(){
		try {
			$data 	= request()->only('kode_akun', 'akun', 'is_pasiva');
			$akun 	= new Account;
			$akun->fill($data);
			$akun->kode_kantor 	= request()->get('kantor_aktif_id');
			$akun->save();

			return redirect()->route('akun.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]);
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}
}