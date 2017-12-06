<?php

namespace App\Http\Controllers\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\MutasiJaminan;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\Aktif;
use Carbon\Carbon, Exception, DB, Config;

class ReportController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function angsuran () 
	{
		$start 		= Carbon::parse('first day of this month')->startOfDay();
		$end 		= Carbon::parse('last day of this month')->endOfDay();

		$angsuran 	= NotaBayar::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));});

		if(request()->has('q')){
			list($start, $end)	= explode(' - ', request()->get('q'));

			$start 	= Carbon::createFromFormat('d/m/Y', $start);
			$end 	= Carbon::createFromFormat('d/m/Y', $end);
		}

		$angsuran 	= $angsuran->where('tanggal', '>=', $start->format('Y-m-d H:i:s'))->where('tanggal', '<=', $end->format('Y-m-d H:i:s'));

		$angsuran 	= $angsuran->displaying()->orderby('tanggal', 'desc')->get();

		$total['pokok'] 	= array_sum(array_column($angsuran->toArray(), 'pokok'));
		$total['bunga'] 	= array_sum(array_column($angsuran->toArray(), 'bunga'));
		$total['denda'] 	= array_sum(array_column($angsuran->toArray(), 'denda'));
		$total['collector'] = array_sum(array_column($angsuran->toArray(), 'collector'));
		$total['subtotal'] 	= array_sum(array_column($angsuran->toArray(), 'subtotal'));

		view()->share('total', $total);
		view()->share('angsuran', $angsuran);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.report.angsuran');
		return $this->layout;
	}

	public function tunggakan() 
	{
		$today 		= Carbon::now();

		if(request()->has('q')){
			$today	= Carbon::createFromFormat('d/m/Y', request()->get('q'));
		}

		$tunggakan 	= AngsuranDetail::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($today)->with(['kredit', 'kredit.penagihan'])->orderby('tanggal', 'asc')->get();

		view()->share('tunggakan', $tunggakan);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.report.tunggakan');
		return $this->layout;
	}

	public function penagihan() 
	{
		$today 		= Carbon::now();

		$penagihan 	= Penagihan::whereHas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));});

		if(request()->has('q')){
			list($start, $end)	= explode(' - ', request()->get('q'));

			$penagihan 	= $penagihan->where('collected_at', '>=', Carbon::createFromFormat('d/m/Y', $start)->format('Y-m-d H:i:s'))->where('collected_at', '<=', Carbon::createFromFormat('d/m/Y', $end)->format('Y-m-d H:i:s'));
		}

		$penagihan 	= $penagihan->hitungTunggakan()->orderby('collected_at', 'desc')->paginate();

		view()->share('penagihan', $penagihan);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.report.penagihan');
		return $this->layout;
	}

	public function jaminan() 
	{
		$start 		= Carbon::now()->startOfDay();
		$end 		= Carbon::now()->endOfDay();

		$jaminan 	= MutasiJaminan::where('kode_kantor', request()->get('kantor_aktif_id'));

		if(request()->has('q')){
			list($start, $end)	= explode(' - ', request()->get('q'));
			$start 	= Carbon::createFromFormat('d/m/Y', $start);
			$end 	= Carbon::createFromFormat('d/m/Y', $end);
		}

		$jaminan 	= $jaminan->where('taken_at', '>=', $start->format('Y-m-d H:i:s'))->where('taken_at', '<=', $end->format('Y-m-d H:i:s'))->orderby('taken_at', 'desc')->paginate();

		view()->share('jaminan', $jaminan);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('kredit.report.jaminan');
		return $this->layout;
	}
}
