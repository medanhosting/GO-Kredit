<?php

namespace App\Http\Controllers\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\MutasiJaminan;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\Angsuran;
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
		$today 		= Carbon::now();

		$angsuran 	= Angsuran::wherenotnull('paid_at')->countAmount()->where('kode_kantor', request()->get('kantor_aktif_id'));

		if(request()->has('q')){
			list($start, $end)	= explode(' - ', request()->get('q'));

			$angsuran 	= $angsuran->where('paid_at', '>=', Carbon::createFromFormat('d/m/Y', $start)->format('Y-m-d H:i:s'))->where('paid_at', '<=', Carbon::createFromFormat('d/m/Y', $end)->format('Y-m-d H:i:s'));
		}

		$angsuran 	= $angsuran->orderby('paid_at', 'desc')->paginate();

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

		$tunggakan 	= Aktif::cektunggakan($today)->where('kode_kantor', request()->get('kantor_aktif_id'));

		$tunggakan 	= $tunggakan->orderby('issued_at', 'desc')->paginate();

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
