<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller as BaseController;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Jaminan;
use Thunderlabid\Manajemen\Models\Kantor;

use App\Http\Service\UI\UploadBase64Gambar;
use Thunderlabid\Pengajuan\Traits\IDRTrait;

use Exception, Response, DB, Carbon\Carbon;

class PermohonanController extends BaseController
{
	use IDRTrait;

	public function store()
	{
		try {
			//1. find perfect location
			if(request()->has('nip_karyawan')){
				$data_input['nip_ao']		= request()->get('nip_karyawan');
				$data_input['kode_kantor']	= request()->get('kode_kantor');
			}
			else{
				$location 			= request()->get('lokasi');
				$semua_koperasi		= Kantor::whereIn('jenis', ['bpr', 'koperasi'])->get();
				$min_distance 		= null;

				foreach ($semua_koperasi as $key => $value) 
				{
					$selisih 		= $this->count_distance(($value['geolocation']['latitude'] - $location['latitude']), ($value['geolocation']['latitude'] - $location['latitude']));

					if($selisih < $min_distance || is_null($min_distance))
					{
						$min_distance 				= $selisih;
						$data_input['kode_kantor']	= $value['id'];
					}
				}
			}

			$data_input['pokok_pinjaman'] 		= request()->get('pokok_pinjaman');
			$data_input['kemampuan_angsur'] 	= request()->get('kemampuan_angsur');
			$data_input['is_mobile'] 			= true;
			$data_input['nasabah']				= request()->get('nasabah'); 
			$data_input['dokumen_pelengkap']	= request()->get('dokumen_pelengkap'); 

			//upload ktp
			$ktp		= base64_decode($data_input['dokumen_pelengkap']['ktp']);
			$data_ktp	= new UploadBase64Gambar('ktp', ['image' => $ktp]);
			$data_ktp	= $data_ktp->handle();

			$data_input['dokumen_pelengkap']['ktp']				= $data_ktp['url'];

			//upload spesimen ttd
			$ttd		= base64_decode($data_input['dokumen_pelengkap']['tanda_tangan']);
			$data_ttd	= new UploadBase64Gambar('tanda_tangan', ['image' => $ttd]);
			$data_ttd	= $data_ttd->handle();

			$data_input['dokumen_pelengkap']['tanda_tangan']	= $data_ttd['url'];

			//get data jaminan
			$data_jaminan		= request()->get('jaminan');

			//!!!!!!!CHECK AO!!!!!!!//
			DB::beginTransaction();

			$pengajuan 			= new Pengajuan;
			$pengajuan->fill($data_input);
			$pengajuan->save();

			foreach ($data_jaminan as $key => $value) 
			{
				$value['pengajuan_id']	= $pengajuan->id;
				$jaminan 				= new Jaminan;
				$jaminan->fill($value);
				$jaminan->save();
			}

			DB::commit();

			return Response::json(['status' => 'sukses', 'data' => $pengajuan->toArray()]);
		} catch (Exception $e) {
			DB::rollback();
			return Response::json(['status' => 'gagal', 'data' => [], 'pesan' => $e->getMessage()]);
		}
	}

	public function index()
	{
		try {
			//1. find pengajuan
			if(request()->has('nip_karyawan')){
				$pengajuan	= Pengajuan::status('permohonan')->where('ao->nip', request()->get('nip_karyawan'))->get();
			}
			else{
				$phone		= request()->get('mobile');
				$pengajuan	= Pengajuan::status('permohonan')->where('nasabah->telepon', $phone['telepon'])->get();
			}

			return Response::json(['status' => 'sukses', 'data' => $pengajuan->toArray()]);

		} catch (Exception $e) {
			return Response::json(['status' => 'gagal', 'data' => [], 'pesan' => $e->getMessage()]);
		}
	}

	public function simulasi($mode)
	{
		$rincian 	= [];

		if(request()->has('kemampuan_angsur') && request()->has('pokok_pinjaman'))
		{
			if($mode=='pa')
			{
				$rincian['pokok_pinjaman']		= request()->get('pokok_pinjaman');
				$rincian['kemampuan_angsur']	= request()->get('kemampuan_angsur');

				if(request()->has('bunga_per_bulan'))
				{
					$rincian['bunga_per_bulan']	= request()->get('bunga_per_bulan');
				}
				else
				{
					$rincian['bunga_per_bulan']	=  (rand(100,500)/1000);
				}

				$k_angs 		= $this->formatMoneyFrom($rincian['kemampuan_angsur']);
				$p_pinjaman 	= $this->formatMoneyFrom($rincian['pokok_pinjaman']);

				//bunga tahunan
				$total_bunga  	= $p_pinjaman * $rincian['bunga_per_bulan'];
				$bulan 			= ceil(($p_pinjaman + $total_bunga)/$k_angs);

				//kredit diusulkan
				$kredit_update 	= $bulan * $k_angs;

				$rincian['lama_angsuran']	= $bulan;
				$rincian['provisi']			= $this->formatMoneyTo((0.5 * $p_pinjaman)/100);
				$rincian['administrasi']	= $this->formatMoneyTo(10000);
				$rincian['legal']			= $this->formatMoneyTo(50000);
				$rincian['pinjaman_bersih']	= $this->formatMoneyTo($p_pinjaman - ((0.5 * $p_pinjaman)/100) - 60000);
				$sisa_pinjaman 				= $p_pinjaman;

				foreach (range(1, $bulan) as $k) 
				{
					$angsuran_bulanan 	= min(ceil(($p_pinjaman/$bulan)/100) * 100, $sisa_pinjaman);
					$angsuran_bunga 	= floor(($total_bunga/$bulan)/100) * 100;

					$sisa_pinjaman 		= $sisa_pinjaman - $angsuran_bulanan;

					$rincian['angsuran'][$k]['bulan']			= Carbon::now()->addmonths($k)->format('M/Y');
					$rincian['angsuran'][$k]['angsuran_bunga']	= $this->formatMoneyTo($angsuran_bunga);
					$rincian['angsuran'][$k]['angsuran_pokok']	= $this->formatMoneyTo($angsuran_bulanan);
					$rincian['angsuran'][$k]['total_angsuran']	= $this->formatMoneyTo($angsuran_bulanan + $angsuran_bunga);
					$rincian['angsuran'][$k]['sisa_pinjaman']	= $this->formatMoneyTo($sisa_pinjaman);
				}
			}
		}

		return Response::json(['status' => 'sukses', 'data' => $rincian]);
	}

	private function count_distance($delta_lat, $delta_lon)
    {
		$earth_radius = 6372.795477598;

		$alpha    = $delta_lat/2;
		$beta     = $delta_lon/2;
		$a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($this->lat_a)) * cos(deg2rad($this->lat_b)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
		$c        = asin(min(1, sqrt($a)));
		$distance = 2*$earth_radius * $c;
		$distance = round($distance, 4);

		return $distance;
	}
}
