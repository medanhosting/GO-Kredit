<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;

use Exception;
use Session;
use MessageBag;

class PengajuanController extends Controller
{
	protected $view_dir = 'pengajuan.';

	public function index ($status) 
	{
		$order 		= 'Tanggal ';
		$urut 		= 'asc';

		if (request()->has('order'))
		{
			list($field, $urut) 	= explode('-', request()->get('order'));
		}

		if (str_is($urut, 'asc'))
		{
			$order 	= $order.' terbaru';
		}
		else
		{
			$order 	= $order.' terlama';
		}

		$pengajuan 				= Pengajuan::status($status)->kantor(request()->get('kantor_aktif_id'))->with(['status_terakhir', 'jaminan_kendaraan', 'jaminan_tanah_bangunan', 'status_permohonan']);

		if (request()->has('q'))
		{
			$cari 				= request()->get('q');
			$pengajuan 			= $pengajuan->where(function($q)use($cari)
			{				
							$q
							// ->whereRaw('lower(nasabah) like ?', '%'.$cari.'%')
							->where('nasabah->nama', 'like', '%'.$cari.'%')
							->orwhere('id', 'like', '%'.$cari.'%');
			});
		}

		$pengajuan 				= $pengajuan->orderby('created_at', $urut)->paginate();

		view()->share('pengajuan', $pengajuan);
		view()->share('status', $status);
		view()->share('order', $order);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view($this->view_dir . 'index');
		return $this->layout;
	}
}
