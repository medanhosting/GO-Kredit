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
		$akun	= Account::where('kode_kantor', request()->get('kantor_aktif_id'))->orderby('nomor_perkiraan', 'asc')->get();

		view()->share('active_submenu', 'akun');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.akun.index', compact('akun'));
		return $this->layout;
	}

	public function store(){
		try {
			$parent = Account::where('nomor_perkiraan', request()->get('akun_nomor_perkiraan'))->where('kode_kantor', request()->get('kantor_aktif_id'))->first();

			$data 	= request()->only('nomor_perkiraan', 'akun');
			$akun 	= new Account;
			$akun->fill($data);
			if($parent){
				$akun->akun_id 	= $parent['id'];
			}
			$akun->kode_kantor 	= request()->get('kantor_aktif_id');
			$akun->save();

			return redirect()->route('akun.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]);
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}
}