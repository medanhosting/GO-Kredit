<?php

namespace App\Http\Controllers\V2\Test;

use App\Http\Controllers\Controller;

use Thunderlabid\Finance\Models\COA;
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
		$pcodes 	= [
			['nomor_perkiraan'	=> '100.000',
			'akun'				=> 'KAS'],
			['nomor_perkiraan'	=> '110.000',
			'akun'				=> 'ANTAR BANK AKTIVA'],
			['nomor_perkiraan'	=> '120.000',
			'akun'				=> 'PINJAMAN YANG DIBERIKAN'],
			['nomor_perkiraan'	=> '130.000',
			'akun'				=> 'PENYISIHAN PENGHAPUSAN AKTIVA PRODUKTIF'],
			['nomor_perkiraan'	=> '140.000',
			'akun'				=> 'PIUTANG BUNGA'],
			['nomor_perkiraan'	=> '150.000',
			'akun'				=> 'AKTIVA TETAP & INVENTARIS'],
			['nomor_perkiraan'	=> '160.000',
			'akun'				=> 'ANTAR KANTOR AKTIVA'],
			['nomor_perkiraan'	=> '170.000',
			'akun'				=> 'RUPA-RUPA AKTIVA'],
			['nomor_perkiraan'	=> '180.000',
			'akun'				=> 'AYAT SILANG (REKENING ANTARA)'],
			['nomor_perkiraan'	=> '200.000',
			'akun'				=> 'KEWAJIBAN'],
			['nomor_perkiraan'	=> '260.000',
			'akun'				=> 'PENDAPATAN YANG DITANGGUHKAN'],
			['nomor_perkiraan'	=> '400.000',
			'akun'				=> 'PENDAPATAN'],
		];

		$codes 	= [
			['nomor_perkiraan'	=> '100.100',
			'akun'				=> 'Kas Besar',
			'parent'			=> '100.000'],
			['nomor_perkiraan'	=> '100.110',
			'akun'				=> 'Teller A',
			'parent'			=> '100.100'],
			['nomor_perkiraan'	=> '100.120',
			'akun'				=> 'Teller B',
			'parent'			=> '100.100'],
			['nomor_perkiraan'	=> '100.200',
			'akun'				=> 'Kas Kecil',
			'parent'			=> '100.000'],
			['nomor_perkiraan'	=> '100.300',
			'akun'				=> 'Kas Titipan',
			'parent'			=> '100.000'],
			['nomor_perkiraan'	=> '100.310',
			'akun'				=> 'Kolektor',
			'parent'			=> '100.300'],


			['nomor_perkiraan'	=> '120.100',
			'akun'				=> 'Pinjaman Angsuran',
			'parent'			=> '120.000'],
			['nomor_perkiraan'	=> '120.200',
			'akun'				=> 'Pinjaman Tetap',
			'parent'			=> '120.000'],
			['nomor_perkiraan'	=> '120.300',
			'akun'				=> 'Pokok PA Jatuh Tempo',
			'parent'			=> '120.000'],
			['nomor_perkiraan'	=> '120.400',
			'akun'				=> 'Pokok PT Jatuh Tempo',
			'parent'			=> '120.000'],


			['nomor_perkiraan'	=> '140.100',
			'akun'				=> 'Piutang Bunga Pinjaman Angsuran',
			'parent'			=> '140.000'],
			['nomor_perkiraan'	=> '140.200',
			'akun'				=> 'Piutang Bunga Pinjaman Tetap',
			'parent'			=> '140.000'],

			['nomor_perkiraan'	=> '140.600',
			'akun'				=> 'Piutang Denda',
			'parent'			=> '140.000'],

			['nomor_perkiraan'	=> '200.200',
			'akun'				=> 'Titipan',
			'parent'			=> '200.000'],
			['nomor_perkiraan'	=> '200.210',
			'akun'				=> 'Titipan Angsuran',
			'parent'			=> '200.200'],

			['nomor_perkiraan'	=> '200.230',
			'akun'				=> 'Titipan Biaya Notaris',
			'parent'			=> '200.200'],

			['nomor_perkiraan'	=> '260.110',
			'akun'				=> 'PYD Bunga',
			'parent'			=> '260.000'],
			['nomor_perkiraan'	=> '260.120',
			'akun'				=> 'PYD Denda',
			'parent'			=> '260.000'],


			['nomor_perkiraan'	=> '401.000',
			'akun'				=> 'Pendapatan Operasional',
			'parent'			=> '400.000'],
			
			['nomor_perkiraan'	=> '401.100',
			'akun'				=> 'Pendapatan Bunga',
			'parent'			=> '401.000'],
			['nomor_perkiraan'	=> '401.120',
			'akun'				=> 'Pihak Ketiga Bukan Bank',
			'parent'			=> '401.000'],
			['nomor_perkiraan'	=> '401.121',
			'akun'				=> 'Bunga Pinjaman Angsuran',
			'parent'			=> '401.120'],
			['nomor_perkiraan'	=> '401.122',
			'akun'				=> 'Bunga Pinjaman Tetap',
			'parent'			=> '401.120'],
			['nomor_perkiraan'	=> '401.123',
			'akun'				=> 'Bunga Dari Pelunasan Sebelum Jatuh Tempo (Dipercepat)',
			'parent'			=> '401.120'],

			['nomor_perkiraan'	=> '401.200',
			'akun'				=> 'Provisi dan Administrasi',
			'parent'			=> '401.000'],
			['nomor_perkiraan'	=> '401.201',
			'akun'				=> 'Provisi',
			'parent'			=> '401.200'],
			['nomor_perkiraan'	=> '401.202',
			'akun'				=> 'Administrasi Pinjaman',
			'parent'			=> '401.200'],
			['nomor_perkiraan'	=> '401.300',
			'akun'				=> 'Pendapatan Operasional Lainnya',
			'parent'			=> '401.000'],
			['nomor_perkiraan'	=> '401.301',
			'akun'				=> 'Denda',
			'parent'			=> '401.300'],
			['nomor_perkiraan'	=> '401.303',
			'akun'				=> 'Legal',
			'parent'			=> '401.300'],
			['nomor_perkiraan'	=> '401.305',
			'akun'				=> 'Bunga & Denda yang Dihapusbukukan',
			'parent'			=> '401.300'],
			['nomor_perkiraan'	=> '401.205',
			'akun'				=> 'Biaya Notaris',
			'parent'			=> '401.200'],
		];

		foreach ($pcodes as $k => $v) {
			$acc 		= COA::where('kode_kantor', request()->get('kantor_aktif_id'))->where('nomor_perkiraan', $v['nomor_perkiraan'])->first();

			if(!$acc){
				$acc	= new COA;
			}

			$acc->kode_kantor 			= request()->get('kantor_aktif_id');
			$acc->nomor_perkiraan 		= $v['nomor_perkiraan'];
			$acc->akun 					= $v['akun'];
			$acc->save();
		}

		foreach ($codes as $k => $v) {
			$parent 	= COA::where('kode_kantor', request()->get('kantor_aktif_id'))->where('nomor_perkiraan', $v['parent'])->first();
			$acc 		= COA::where('kode_kantor', request()->get('kantor_aktif_id'))->where('nomor_perkiraan', $v['nomor_perkiraan'])->first();

			if(!$acc){
				$acc	= new COA;
			}

			$acc->kode_kantor 			= request()->get('kantor_aktif_id');
			$acc->nomor_perkiraan 		= $v['nomor_perkiraan'];
			$acc->akun 					= $v['akun'];
			$acc->coa_id 				= $parent->id;
			$acc->save();
		}

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
			if(request()->get('type')=='jp'){
				Artisan::call('gokredit:jurnalpagi', ['--tanggal' => request()->get('q')]);
				return redirect()->back()->withErrors(['Memorial telah dikeluarkan']);
			}else{
				Artisan::call('gokredit:terbitkansp', ['--tanggal' => request()->get('q')]);
				return redirect()->back()->withErrors(['SP telah dikeluarkan']);
			}
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