<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Analisa;
use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Pengajuan\Models\LegalRealisasi;
use Thunderlabid\Survei\Models\Survei;

use Thunderlabid\Log\Models\Kredit;

use Exception;
use Session;
use MessageBag;

class PengajuanController extends Controller
{
	protected $view_dir = 'pengajuan.';

	public function __construct()
	{
		parent::__construct();

		$this->middleware('scope:'.request()->segment(2));
		
		$this->middleware('required_password')->only('destroy');
	}

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

	public function show($status, $id)
	{
		try {
			$permohonan		= Pengajuan::where('id', $id)->status($status)->kantor(request()->get('kantor_aktif_id'))->with('jaminan_kendaraan', 'jaminan_tanah_bangunan', 'riwayat_status', 'status_terakhir')->first();

			$breadcrumb 	= [
				[
					'title'	=> $status,
					'route' => route('pengajuan.pengajuan.index', ['status' => $status])
				], 
				[
					'title'	=> $id,
					'route' => route('pengajuan.pengajuan.index', ['status' => $status, 'id' => $id])
				]
			];

			if (!$permohonan)
			{
				throw new Exception("Data tidak ada!", 1);
			}
			
			$survei 		= Survei::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->with(['character', 'condition', 'capacity', 'capital', 'collateral'])->get()->toArray();
			$analisa 		= Analisa::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->get()->toArray();
			$putusan 		= Putusan::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->get()->toArray();

			$r_nasabah 		= $this->riwayat_kredit_nasabah($permohonan['nasabah']['nik'], $id);
			$r_jaminan 		= $this->riwayat_kredit_jaminan($permohonan['jaminan_kendaraan'], $permohonan['jaminan_tanah_bangunan'], $id);

			view()->share('permohonan', $permohonan);
			view()->share('survei', $survei);
			view()->share('analisa', $analisa);
			view()->share('putusan', $putusan);

			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
			view()->share('breadcrumb', $breadcrumb);
			view()->share('status', $status);

			$this->layout->pages 	= view('pengajuan.show', compact('r_nasabah', 'r_jaminan'));
			return $this->layout;

		} catch (Exception $e) {
			return redirect(route('pengajuan.pengajuan.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
		}
	}

	public function destroy($status, $id)
	{
		try {
			$permohonan		= Pengajuan::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->with('jaminan')->first();
			if (!$permohonan)
			{
				throw new Exception("Data tidak ada!", 1);
			}

			foreach ($permohonan->jaminan as $key => $value)
			{
				$value->delete();
			}

			$permohonan->delete();

			return redirect(route('pengajuan.pengajuan.index', ['status' => $status, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]));
		} catch (Exception $e) {
			return redirect(route('pengajuan.pengajuan.index', ['status' => $status, 'id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
		}
	}

	public function print($id, $mode)
	{
		$realisasi 				= LegalRealisasi::where('pengajuan_id', $id)->where('jenis', $mode)->first()->toArray();

		return view('pengajuan.print.'.$mode, compact('realisasi'));
	}

	private function riwayat_kredit_nasabah($nik, $id)
	{
		$k_ids	= array_column(Kredit::where('nasabah_id', $nik)->where('pengajuan_id', '<>', $id)->get()->toArray(), 'pengajuan_id');

		return Pengajuan::wherein('id', $k_ids)->get();
	}

	private function riwayat_kredit_jaminan($kendaraan, $tanah_bangunan, $id)
	{
		$w_id 	= [];

		foreach ($kendaraan as $key => $value) {
			$w_id[] = $value['dokumen_jaminan']['bpkb']['nomor_bpkb'];
		}

		$w_id  		= array_unique($w_id);
		$k_ids_1	= array_column(Kredit::whereIn('jaminan_id', $w_id)->where('jaminan_tipe', 'bpkb')->where('pengajuan_id', '<>', $id)->get(['pengajuan_id'])->toArray(), 'pengajuan_id');

		foreach ($tanah_bangunan as $key => $value) {
			$w_id[] = $value['dokumen_jaminan'][$value['jenis']]['nomor_sertifikat'];
		}
		$w_id 	 	= array_unique($w_id);
		$k_ids_2	= array_column(Kredit::whereIn('jaminan_id', $w_id)->whereIn('jaminan_tipe', ['shgb', 
			'shm'])->where('pengajuan_id', '<>', $id)->get(['pengajuan_id'])->toArray(), 'pengajuan_id');

		$k_ids 		= array_unique(array_merge($k_ids_1, $k_ids_2));

		return Pengajuan::wherein('id', $k_ids)->get();
	}
}
