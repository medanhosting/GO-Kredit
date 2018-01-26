<?php

namespace App\Http\Controllers\V2\Finance;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Exception, DB, Auth, Carbon\Carbon;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Finance\Models\Account;
use Thunderlabid\Finance\Models\TransactionDetail;

class KasirController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->middleware('scope:kasir');
	}

	public function lkh() 
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

	public function jurnalpagi() 
	{
		$today 	= Carbon::now();
		if(request()->has('q')){
			$today 	= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}
		$sday 	= $today->startofday()->format('Y-m-d H:i:s');
		$eday 	= $today->startofday()->format('Y-m-d H:i:s');

		$kredit 	= Aktif::wherehas('angsuran', function($q)use($sday){$q->where(function($q)use($sday){
				$q
				->wherenull('nota_bayar_id')
				->orwhereraw(\DB::raw('(select nb.tanggal from k_nota_bayar as nb where nb.id = k_angsuran_detail.nota_bayar_id and nb.tanggal >= "'.$sday.'" limit 1) >= k_angsuran_detail.tanggal'))
				;
			});})->with(['angsuran_terakhir', 'angsuran' => function($q)use($sday){$q
			->selectraw(\DB::raw('SUM(IF(tag="pokok",amount,0)) as pokok'))
			->selectraw(\DB::raw('SUM(IF(tag="bunga",amount,0)) as bunga'))
			->selectraw(\DB::raw('nth'))
			->selectraw(\DB::raw('nomor_kredit'))
			->wherein('tag', ['pokok', 'bunga'])
			->where('tanggal', '>=', $sday)
			->groupby('nth')
			->orderby('nth', 'asc')
			->groupby('nomor_kredit')
			;},
			'tunggakan' 	=> function($q)use($eday){$q
			->selectraw(\DB::raw('SUM(IF(tag="pokok",amount,0)) as pokok'))
			->selectraw(\DB::raw('SUM(IF(tag="bunga",amount,0)) as bunga'))
			->selectraw(\DB::raw('min(nth) as nth'))
			->selectraw(\DB::raw('count(nth) as tgk'))
			->selectraw(\DB::raw('nomor_kredit'))
			->TunggakanBeberapaWaktuLalu(Carbon::parse($eday))
			->groupby('nomor_kredit')
			;},
			'denda' 	=> function($q)use($sday){$q
			->selectraw(\DB::raw('SUM(IF(tag="denda",amount,IF(tag="restitusi_denda",amount,0))) as denda'))
			->selectraw(\DB::raw('nomor_kredit'))
			->where('tanggal', '<', $sday)
			->groupby('nomor_kredit')
			;},
			'titipan' 	=> function($q)use($sday){$q
			->selectraw(\DB::raw('SUM(IF(tag="titipan",amount,IF(tag="pengambilan_titipan",amount,0))) as titipan'))
			->selectraw(\DB::raw('nomor_kredit'))
			->where('tanggal', '<', $sday)
			->groupby('nomor_kredit')
			;},
			])->paginate();

		view()->share('active_submenu', 'jurnalpagi');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.kasir.jurnalpagi', compact('kredit'));
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

		view()->share('html', ['title' => 'LAPORAN KASIR HARIAN']);

		return view('v2.print.finance.kasir.kas_harian');
	}
}