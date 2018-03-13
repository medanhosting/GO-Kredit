<?php

namespace App\Http\Controllers\V2\Finance;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Exception, DB, Auth, Carbon\Carbon;

use Thunderlabid\Finance\Models\COA;
use Thunderlabid\Finance\Models\Jurnal;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;

use App\Service\Traits\IDRTrait;

use App\Http\Middleware\LimitDateMiddleware;

class JurnalController extends Controller
{
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();
		
		$this->middleware('scope:'.implode('|', $this->acl_menu['keuangan.jurnal']))->only(['index']);
		$this->middleware('scope:'.implode('|', $this->acl_menu['keuangan.kas']))->only(['store']);
	}

	public function index () 
	{
		$tanggal 	= Carbon::now();

		if(request()->has('q')){
			$tanggal= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$tanggal->startofday()->addhours(15);

		$akun 		= COA::wherenull('coa_id')->where('kode_kantor', request()->get('kantor_aktif_id'))->with(['subakun', 'subakun.subakun', 'subakun.detailsin' => function($q)use($tanggal){$q->where('f_jurnal.tanggal', '<=', $tanggal->format('Y-m-d H:i:s'));}, 'subakun.detailsout' => function($q)use($tanggal){$q->where('f_jurnal.tanggal', '<=', $tanggal->format('Y-m-d H:i:s'));}])->wherehas('subakun', function($q){$q;})->get()->toarray();

		foreach ($akun as $k => $v) {
			foreach ($v['subakun'] as $k2 => $v2) {
				if(count($v2['subakun'])){
					$str_pos 	= strspn($v2['subakun'][0]['nomor_perkiraan'] ^ $v2['nomor_perkiraan'], "\0");
					$likely		= substr($v2['nomor_perkiraan'], 0, $str_pos);

					$akun[$k]['subakun'][$k2]['detailsin'] 	= Jurnal::wherehas('coa', function($q)use($likely){$q->where('kode_kantor', request()->get('kantor_aktif_id'))->where('nomor_perkiraan', 'like', $likely.'%'); })
					->where('f_jurnal.jumlah', '>=', 0)
					->where('f_jurnal.tanggal', '<=', $tanggal->format('Y-m-d H:i:s'))
					->selectraw('f_jurnal.*')->selectraw('f_jurnal.jumlah as amount')
					->get()->toarray();

					$akun[$k]['subakun'][$k2]['detailsout']	= Jurnal::wherehas('coa', function($q)use($likely){$q->where('kode_kantor', request()->get('kantor_aktif_id'))->where('nomor_perkiraan', 'like', $likely.'%'); })
					->where('f_jurnal.jumlah', '<=', 0)
					->where('f_jurnal.tanggal', '<=', $tanggal->format('Y-m-d H:i:s'))
					->selectraw('f_jurnal.*')->selectraw('f_jurnal.jumlah as amount')
					->get()->toarray();
				}
			}
		}


		view()->share('active_submenu', 'jurnal');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.jurnal.index', compact('akun', 'tanggal'));
		return $this->layout;
	}

	public function store(){
		try {
			DB::beginTransaction();

			$nb 	= NotaBayar::where('nomor_faktur', request()->get('nomor_faktur'))->where('nomor_faktur', 'like', request()->get('kantor_aktif_id').'%')->first();

			request()->merge(['tanggal' => $nb->tanggal]);
			LimitDateMiddleware::check(implode('|', $this->scopes['scopes']), 'kas');

			if($nb){
				$coa_deb 	= request()->get('nomor_perkiraan_deb');
				$coa_kre 	= request()->get('nomor_perkiraan_kre');
				foreach ($coa_deb as $k => $v) {

					//save deb
					$dt 	= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('id', $k)->selectraw('*')->selectraw('jumlah as total')->first();

					$coa_d 	= COA::where('nomor_perkiraan', $v)->where('kode_kantor', request()->get('kantor_aktif_id'))->first();
					$coa_k 	= COA::where('nomor_perkiraan', $coa_kre[$k])->where('kode_kantor', request()->get('kantor_aktif_id'))->first();
					
					$j_deb 	= Jurnal::where('detail_transaksi_id', $dt->id)->where('jumlah', '>', 0)->first();
					if(!$j_deb){
						$j_deb 	= new Jurnal;
					}
					$j_deb->detail_transaksi_id = $dt->id;
					$j_deb->tanggal 			= $nb->tanggal;
					$j_deb->coa_id 				= $coa_d->id;
					$j_deb->jumlah 				= $this->formatMoneyTo(abs($dt['total']));
					$j_deb->save();

					$j_kre 	= Jurnal::where('detail_transaksi_id', $dt->id)->where('jumlah', '<', 0)->first();
					if(!$j_kre){
						$j_kre 	= new Jurnal;
					}
					$j_kre->detail_transaksi_id = $dt->id;
					$j_kre->tanggal 			= $nb->tanggal;
					$j_kre->coa_id 				= $coa_k->id;
					$j_kre->jumlah 				= $this->formatMoneyTo(0 - abs($dt['total']));
					$j_kre->save();
				}
			}
		
			DB::commit();
			return redirect()->route('kas.show', ['id' => $nb['id'], 'kantor_aktif_id' => request()->get('kantor_aktif_id')]);

		} catch (Exception $e) {
			dd($e);
			DB::rollback();
			return redirect()->route('kas.show', ['id' => $nb['id'], 'kantor_aktif_id' => request()->get('kantor_aktif_id')]);
		}
	}

	public function print ($id)
	{
		$tanggal 	= Carbon::now();

		if(request()->has('q')){
			$tanggal= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$tanggal->startofday()->addhours(15);

		$akun 		= COA::wherenull('coa_id')->where('id', $id)->where('kode_kantor', request()->get('kantor_aktif_id'))->with(['subakun', 'subakun.subakun', 'subakun.detailsin' => function($q)use($tanggal){$q->where('f_jurnal.tanggal', '<=', $tanggal->format('Y-m-d H:i:s'));}, 'subakun.detailsout' => function($q)use($tanggal){$q->where('f_jurnal.tanggal', '<=', $tanggal->format('Y-m-d H:i:s'));}])->wherehas('subakun', function($q){$q;})->get()->toarray();

		foreach ($akun as $k => $v) {
			foreach ($v['subakun'] as $k2 => $v2) {
				if(count($v2['subakun'])){
					$str_pos 	= strspn($v2['subakun'][0]['nomor_perkiraan'] ^ $v2['nomor_perkiraan'], "\0");
					$likely		= substr($v2['nomor_perkiraan'], 0, $str_pos);

					$akun[$k]['subakun'][$k2]['detailsin'] 	= Jurnal::wherehas('coa', function($q)use($likely){$q->where('kode_kantor', request()->get('kantor_aktif_id'))->where('nomor_perkiraan', 'like', $likely.'%'); })
					->where('f_jurnal.jumlah', '>=', 0)
					->where('f_jurnal.tanggal', '<=', $tanggal->format('Y-m-d H:i:s'))
					->selectraw('f_jurnal.*')->selectraw('f_jurnal.jumlah as amount')
					->get()->toarray();

					$akun[$k]['subakun'][$k2]['detailsout']	= Jurnal::wherehas('coa', function($q)use($likely){$q->where('kode_kantor', request()->get('kantor_aktif_id'))->where('nomor_perkiraan', 'like', $likely.'%'); })
					->where('f_jurnal.jumlah', '<=', 0)
					->where('f_jurnal.tanggal', '<=', $tanggal->format('Y-m-d H:i:s'))
					->selectraw('f_jurnal.*')->selectraw('f_jurnal.jumlah as amount')
					->get()->toarray();

					break;
				}
			}
		}

		view()->share('akun', $akun);
		view()->share('type', $type);
		view()->share('id', $id);
		view()->share('html', ['title' => 'JURNAL ' . strtoupper($type)]);

		return view('v2.print.finance.jurnal.kas_or_bank', compact('tanggal'));
	}
}