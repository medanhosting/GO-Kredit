<?php

namespace App\Http\Controllers\V2\Finance;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use Thunderlabid\Finance\Models\TransactionDetail;

use App\Service\Traits\IDRTrait;

use App\Http\Controllers\V2\Traits\AkunTrait;

use App\Http\Service\Policy\PelunasanAngsuran;

use Exception, DB, Auth, Carbon\Carbon;

class BankController extends Controller
{
	use IDRTrait;
	use AkunTrait;

	public function __construct()
	{
		parent::__construct();
	}

	public function penerimaan() 
	{
		$akun 		= $this->get_akun(request()->get('kantor_aktif_id'));

		$angsuran 	= NotaBayar::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('nomor_perkiraan', 'like', '110.%');
		$today 		= Carbon::now();

		if(request()->has('q')){
			$today		= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$angsuran 	= $angsuran->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'));

		$angsuran 		= $angsuran->wherehas('details', function($q){$q;})->Displaying()->get();

		$total_money 	= TransactionDetail::wherehas('account', function($q){$q->where('nomor_perkiraan', 'like', '110.%')->wherenotnull('f_account.akun_id')->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))->sum('amount');

		$total_jatuh_tempo 	= NotaBayar::wherehas('details', function($q){$q->whereIn('tag', ['pokok', 'bunga']);})->wheredoesnthave('details', function($q){$q->whereIn('tag', ['potongan']);})->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))->where('nomor_perkiraan', 'like', '110.%')->sum('jumlah');

		$total_pelunasan= NotaBayar::wherehas('details', function($q){$q->whereIn('tag', ['potongan']);})->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))->where('nomor_perkiraan', 'like', '110.%')->sum('jumlah');

		$total_money_yesterday 	= TransactionDetail::wherehas('account', function($q){$q->where('nomor_perkiraan', 'like', '110.%')->wherenotnull('f_account.akun_id')->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('tanggal', '<', $today->startofday()->format('Y-m-d H:i:s'))->sum('amount');
		
		$total_angsuran 	= NotaBayar::wherehas('details', function($q){$q->whereIn('tag', ['pokok', 'bunga']);})->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))->where('nomor_perkiraan', 'like', '110.%')->sum('jumlah');

		$amount 			= NotaBayar::where('nomor_perkiraan', 'like', '110.%')->groupby('nomor_perkiraan')->selectraw('nomor_perkiraan')->selectraw(\DB::raw('sum(IF(tanggal < "'.$today->startofday()->format('Y-m-d H:i:s').'",jumlah,0)) as kemarin'))->selectraw(\DB::raw('sum(IF(tanggal >= "'.$today->startofday()->format('Y-m-d H:i:s').'",jumlah,0)) as hari_ini'))->get();

		view()->share('active_submenu', 'bank_masuk');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.bank.penerimaan', compact('angsuran', 'total_money', 'total_jatuh_tempo', 'today', 'total_money_yesterday', 'total_pelunasan', 'total_angsuran', 'amount', 'akun'));
		return $this->layout;
	}

	public function pengeluaran() 
	{
		$angsuran 	= NotaBayar::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('nomor_perkiraan', 'like', '110.%');
		$today 		= Carbon::now();

		if(request()->has('q')){
			$today		= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$angsuran 	= $angsuran->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'));

		$angsuran 		= $angsuran->wherehas('details', function($q){$q;})->where('jumlah', '<', 0)->get();

		$total_money 	= TransactionDetail::wherehas('account', function($q){$q->where('nomor_perkiraan', 'like', '110.%')->wherenotnull('f_account.akun_id')->where('kode_kantor', request()->get('kantor_aktif_id'));})->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))->sum('amount');

		view()->share('active_submenu', 'bank_keluar');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.finance.bank.pengeluaran', compact('angsuran', 'total_money', 'today'));
		return $this->layout;
	}
}
