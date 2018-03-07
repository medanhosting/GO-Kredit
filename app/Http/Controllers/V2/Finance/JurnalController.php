<?php

namespace App\Http\Controllers\V2\Finance;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Exception, DB, Auth, Carbon\Carbon;

use Thunderlabid\Finance\Models\COA;
use Thunderlabid\Finance\Models\Jurnal;

class JurnalController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware('scope:'.implode('|', $this->acl_menu['keuangan.jurnal']))->only(['index']);
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

					break;
				}
			}
		}


		view()->share('active_submenu', 'jurnal');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.jurnal.index', compact('akun', 'tanggal'));
		return $this->layout;
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