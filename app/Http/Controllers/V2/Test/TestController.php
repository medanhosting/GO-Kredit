<?php

namespace App\Http\Controllers\V2\Test;

use App\Http\Controllers\Controller;

use Thunderlabid\Finance\Models\Jurnal;

use Exception, Artisan, Carbon\Carbon;

class TestController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware('scope:'.implode('|', $this->acl_menu['inspektor.audit']));
	}

	public function read_jp () 
	{
		$jurnal 	= Jurnal::selectraw('sum(f_jurnal.jumlah) jumlah')
		->selectraw('min(f_jurnal.id) as id')
		->selectraw('max(f_jurnal.tanggal) as tanggal')
		->selectraw('coa_id')
		->selectraw('f_detail_transaksi.nomor_faktur as nomor_faktur')
		->join('f_detail_transaksi', 'f_detail_transaksi.id', 'detail_transaksi_id')
		->groupby('coa_id')
		->groupby('nomor_faktur')
		->orderby('tanggal', 'desc')
		->orderby('nomor_faktur', 'desc')
		->orderby('id', 'asc')
		->orderby('jumlah', 'desc')
		->with(['coa'])
		->get()
		;

		view()->share('jurnal', $jurnal);

		view()->share('active_submenu', 'test_jurnal');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.test.jurnal.index');
		return $this->layout;
	}

	public function predict_jp () 
	{
		view()->share('active_submenu', 'predict_jurnal');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		if(request()->has('q')){
			Artisan::call('gokredit:jurnalpagi', ['--tanggal' => request()->get('q')]);
			return redirect()->back()->withErrors(['Memorial telah dikeluarkan']);
		}

		$this->layout->pages 	= view('v2.test.jurnal.predict');
		return $this->layout;
	}

	public function rollback_db () 
	{
		view()->share('active_submenu', 'rollback');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		if(request()->has('q')){
			Artisan::call('gokredit:rollbacktransaction', ['--tanggal' => request()->get('q')]);
			return redirect()->back()->withErrors(['Transaksi berhasil dihapus']);
		}

		$this->layout->pages 	= view('v2.test.database.rollback');
		return $this->layout;
	}
}