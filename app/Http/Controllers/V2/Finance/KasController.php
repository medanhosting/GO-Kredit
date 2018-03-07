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
		$notabayar 				= NotaBayar::whereIn('jenis', ['bkk', 'bkm'])->paginate();

		view()->share('active_submenu', 'kas');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kas.index', compact('notabayar'));
		return $this->layout;
	}

	public function create($id = null) 
	{
		$notabayar 				= NotaBayar::whereIn('jenis', ['bkk', 'bkm'])->where('id', $id)->first();

		view()->share('active_submenu', 'kas');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kas.create', compact('notabayar'));
		return $this->layout;
	}

	public function penerimaan($tipe = 'kas') 
	{
		$return  	= $this->getPenerimaan();

		$tanggal 	= $return['tanggal'];
		$kemarin 	= $return['kemarin'];
		$jurnal 	= $return['jurnal'];
		$total 		= $return['total'];
		$p_total 	= $return['p_total'];
		$total_jt 	= $return['total_jt'];
		$total_a 	= $return['total_a'];
		
		view()->share('active_submenu', 'penerimaan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kas.penerimaan', compact('jurnal', 'total', 'p_total', 'total_jt', 'total_a', 'tanggal', 'kemarin'));
		return $this->layout;
	}

	public function pengeluaran() 
	{
		$return  	= $this->getPengeluaran();

		$tanggal 	= $return['tanggal'];
		$kemarin 	= $return['kemarin'];
		$jurnal 	= $return['jurnal'];
		$total 		= $return['total'];

		view()->share('active_submenu', 'pengeluaran');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kas.pengeluaran', compact('jurnal', 'total', 'tanggal', 'kemarin'));
		return $this->layout;
	}


	public function penerimaan_tutup_kas($tipe = 'kas') 
	{
		$return  	= $this->getPenerimaan();

		$tanggal 	= $return['tanggal'];
		$kemarin 	= $return['kemarin'];
		$jurnal 	= $return['jurnal'];
		$total 		= $return['total'];
		$p_total 	= $return['p_total'];
		$total_jt 	= $return['total_jt'];
		$total_a 	= $return['total_a'];

		view()->share('active_submenu', 'penerimaan_tk');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kas.penerimaan_tk', compact('jurnal', 'total', 'p_total', 'total_jt', 'total_a', 'tanggal'));
		return $this->layout;
	}


	public function pengeluaran_tutup_kas() 
	{
		$return  	= $this->getPengeluaran();

		$tanggal 	= $return['tanggal'];
		$kemarin 	= $return['kemarin'];
		$jurnal 	= $return['jurnal'];
		$total 		= $return['total'];

		view()->share('active_submenu', 'pengeluaran_tk');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kas.pengeluaran_tk', compact('jurnal', 'total', 'tanggal'));
		return $this->layout;
	}



	public function print($tipe)
	{
		if (str_is($tipe, 'penerimaan') || str_is($tipe, 'penerimaan_tk'))
		{
			$return  	= $this->getPenerimaan();

			$tanggal 	= $return['tanggal'];
			$kemarin 	= $return['kemarin'];
			$jurnal 	= $return['jurnal'];
			$total 		= $return['total'];
			$p_total 	= $return['p_total'];
			$total_jt 	= $return['total_jt'];
			$total_a 	= $return['total_a'];
		
			return view('v2.print.finance.kas.'.$tipe, compact('jurnal', 'total', 'p_total', 'total_jt', 'total_a', 'tanggal', 'kemarin'));
		} else {
			$return  	= $this->getPengeluaran();

			$tanggal 	= $return['tanggal'];
			$kemarin 	= $return['kemarin'];
			$jurnal 	= $return['jurnal'];
			$total 		= $return['total'];

			return view('v2.print.finance.kas.'.$tipe, compact('jurnal', 'total', 'tanggal', 'kemarin'));
		}
	}


	private function getPenerimaan(){
		$today 		= Carbon::now();

		if(request()->has('q')){
			$today 	= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$today 		= $today->startofday()->addhours(15);
		$yesterday 	= Carbon::parse($today)->subdays(1)->startofday()->addhours(15);

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
		->where('f_nota_bayar.tanggal', '>=', $yesterday->format('Y-m-d H:i:s'))
		->where('f_nota_bayar.tanggal', '<=', $today->format('Y-m-d H:i:s'))
		
		->selectraw('(sum(if(f_coa.nomor_perkiraan="200.210", f_jurnal.jumlah, 0)) * - 1) as titipan')

		->selectraw('(sum(if(f_coa.nomor_perkiraan="120.100", f_jurnal.jumlah, 0)) * - 1) as pokok_pa')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="120.200", f_jurnal.jumlah, 0)) * - 1) as pokok_pt')

		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.121", f_jurnal.jumlah, 0)) * - 1) as bunga_pa')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.122", f_jurnal.jumlah, 0)) * - 1) as bunga_pt')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.123", f_jurnal.jumlah, 0)) * - 1) as lain_lain')

		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.301", f_jurnal.jumlah, 0)) * - 1) as denda')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.201", f_jurnal.jumlah, 0)) * - 1) as provisi')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.202", f_jurnal.jumlah, 0)) * - 1) as administrasi')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="401.303", f_jurnal.jumlah, 0)) * - 1) as legal')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="200.230", f_jurnal.jumlah, 0)) * - 1) as lain_lain')
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
		->where('f_nota_bayar.tanggal', '>=', $yesterday->format('Y-m-d H:i:s'))
		->where('f_nota_bayar.tanggal', '<=', $today->format('Y-m-d H:i:s'))
		->sum('jumlah');

		$p_total= NotaBayar::whereIn('jenis', ['angsuran', 'memorial_kolektor', 'denda', 'setoran_pencairan'])
		->where('f_nota_bayar.tanggal', '<', $yesterday->format('Y-m-d H:i:s'))
		->sum('jumlah');

		//JUMLAH ANGSURAN JATUH TEMPO	
		$total_jt 	= Jurnal::wherehas('coa', function($q){
			$q->whereIn('nomor_perkiraan', ['120.300','120.400','140.100','140.200']);
		})
		->where('tanggal', '>=', $yesterday->format('Y-m-d H:i:s'))
		->where('tanggal', '<=', $today->format('Y-m-d H:i:s'))
		->where('jumlah', '<', 0)->sum('jumlah') * -1;

		$total_a 	= Jurnal::wherehas('coa', function($q){
			$q->whereIn('nomor_perkiraan', ['120.200','401.123']);
		})
		->where('tanggal', '>=', $yesterday->format('Y-m-d H:i:s'))
		->where('tanggal', '<=', $today->format('Y-m-d H:i:s'))
		->where('jumlah', '<', 0)->sum('jumlah') * -1;

		return ['tanggal' => $today, 'kemarin' => $yesterday, 'jurnal' => $jurnal, 'total' => $total, 'p_total' => $p_total, 'total_jt' => $total_jt, 'total_a' => $total_a];
	}


	private function getPengeluaran(){
		$today 		= Carbon::now();

		if(request()->has('q')){
			$today 	= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$today 		= $today->startofday()->addhours(15);
		$yesterday 	= Carbon::parse($today)->subdays(1)->startofday()->addhours(15);

		// PA
		// PT
		$jurnal 	= NotaBayar::whereIn('jenis', ['pencairan'])
		->join('f_detail_transaksi', 'f_detail_transaksi.nomor_faktur', 'f_nota_bayar.nomor_faktur')
		->join('f_jurnal', 'f_jurnal.detail_transaksi_id', 'f_detail_transaksi.id')
		->join('f_coa', 'f_jurnal.coa_id', 'f_coa.id')
		->where('f_nota_bayar.tanggal', '>=', $yesterday->format('Y-m-d H:i:s'))
		->where('f_nota_bayar.tanggal', '<=', $today->format('Y-m-d H:i:s'))

		->selectraw('(sum(if(f_coa.nomor_perkiraan="120.100", f_jurnal.jumlah, 0))) as pokok_pa')
		->selectraw('(sum(if(f_coa.nomor_perkiraan="120.200", f_jurnal.jumlah, 0))) as pokok_pt')

		->selectraw('f_nota_bayar.nomor_faktur')
		->selectraw('max(f_nota_bayar.tanggal) as tanggal')
		->selectraw('max(f_nota_bayar.morph_reference_id) as morph_reference_id')

		->where('f_jurnal.jumlah', '>', 0)
		->groupby('f_nota_bayar.nomor_faktur')
		->orderby('tanggal', 'asc')
		->get()
		;

		//total penerimaan
		$total 	= NotaBayar::whereIn('jenis', ['angsuran', 'memorial_kolektor', 'denda', 'setoran_pencairan', 'pencairan'])
		->where('f_nota_bayar.tanggal', '>=', $yesterday->format('Y-m-d H:i:s'))
		->where('f_nota_bayar.tanggal', '<=', $today->format('Y-m-d H:i:s'))
		->sum('jumlah');

		return ['tanggal' => $today, 'kemarin' => $yesterday, 'jurnal' => $jurnal, 'total' => $total];
	}
}
