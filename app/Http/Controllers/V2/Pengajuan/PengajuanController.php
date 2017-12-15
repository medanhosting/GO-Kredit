<?php

namespace App\Http\Controllers\V2\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;

class PengajuanController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index ($status) 
	{
		$permohonan		= Pengajuan::status('permohonan')->kantor(request()->get('kantor_aktif_id'))->with(['status_terakhir', 'jaminan_kendaraan', 'jaminan_tanah_bangunan', 'status_permohonan']);
		if (request()->has('q'))
		{
			$cari		= request()->get('q');
			$permohonan	= $permohonan->where(function($q)use($cari)
			{				
				$q
				->where('nasabah->nama', 'like', '%'.$cari.'%')
				->orwhere('id', 'like', '%'.$cari.'%');
			});
		}
		$permohonan		= $permohonan->orderby('p_pengajuan.created_at', 'desc')->paginate();

		$survei			= Pengajuan::status('survei')->kantor(request()->get('kantor_aktif_id'))->with(['status_terakhir', 'jaminan_kendaraan', 'jaminan_tanah_bangunan']);
		if (request()->has('q'))
		{
			$cari		= request()->get('q');
			$survei		= $survei->where(function($q)use($cari)
			{				
				$q
				->where('nasabah->nama', 'like', '%'.$cari.'%')
				->orwhere('id', 'like', '%'.$cari.'%');
			});
		}
		$survei			= $survei->orderby('p_pengajuan.created_at', 'desc')->paginate();

		$analisa		= Pengajuan::status('analisa')->kantor(request()->get('kantor_aktif_id'))->with(['status_terakhir', 'jaminan_kendaraan', 'jaminan_tanah_bangunan']);
		if (request()->has('q'))
		{
			$cari		= request()->get('q');
			$analisa	= $analisa->where(function($q)use($cari)
			{				
				$q
				->where('nasabah->nama', 'like', '%'.$cari.'%')
				->orwhere('id', 'like', '%'.$cari.'%');
			});
		}
		$analisa		= $analisa->orderby('p_pengajuan.created_at', 'desc')->paginate();

		$putusan		= Pengajuan::status('putusan')->kantor(request()->get('kantor_aktif_id'))->with(['status_terakhir', 'jaminan_kendaraan', 'jaminan_tanah_bangunan']);
		if (request()->has('q'))
		{
			$cari		= request()->get('q');
			$putusan	= $putusan->where(function($q)use($cari)
			{				
				$q
				->where('nasabah->nama', 'like', '%'.$cari.'%')
				->orwhere('id', 'like', '%'.$cari.'%');
			});
		}
		$putusan		= $putusan->orderby('p_pengajuan.created_at', 'desc')->paginate();

		view()->share('permohonan', $permohonan);
		view()->share('survei', $survei);
		view()->share('analisa', $analisa);
		view()->share('putusan', $putusan);
		view()->share('active_submenu', 'pengajuan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.pengajuan.index');
		return $this->layout;
	}
}
