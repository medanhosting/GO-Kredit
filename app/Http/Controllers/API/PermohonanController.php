<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller as BaseController;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Jaminan;
use Thunderlabid\Manajemen\Models\Kantor;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use App\Service\Api\ResponseTrait;
use App\Http\Service\UI\UploadBase64Gambar;
use App\Http\Service\Policy\PerhitunganBunga;

use Exception, Response, DB, Carbon\Carbon, Auth;

class PermohonanController extends BaseController
{
	use ResponseTrait;

	public function simulasi($mode)
	{
		$rincian 	= [];

		if(request()->has('kemampuan_angsur') && request()->has('pokok_pinjaman'))
		{
			if($mode=='pa')
			{
				$rincian 	= new PerhitunganBunga(request()->get('pokok_pinjaman'), request()->get('kemampuan_angsur'), request()->get('bunga_per_tahun')/12);
				$rincian 	= $rincian->pa();
			}
			elseif($mode=='pt')
			{
				$rincian 	= new PerhitunganBunga(request()->get('pokok_pinjaman'), request()->get('kemampuan_angsur'), request()->get('bunga_per_tahun')/12);
				$rincian 	= $rincian->pt();
			}
		}

		return response()->json(['status' => 1, 'data' => $rincian, 'error' => ['message' => []]]);
	}
	
	public function store()
	{
		try {
			//1. find perfect location
			if(Auth::user()){
				$penempatankaryawan			= PenempatanKaryawan::where('orang_id', Auth::user()['id'])->active(Carbon::now())->first();
				$data_input['nip_ao']		= Auth::user()['nip'];
				$data_input['kode_kantor']	= $penempatankaryawan['kantor_id'];
			}
			else{
				$location 			= request()->get('geolocation');
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

			if(request()->has('pokok_pinjaman'))
			{
				$data_input['pokok_pinjaman'] 		= request()->get('pokok_pinjaman');
				$data_input['kemampuan_angsur'] 	= request()->get('kemampuan_angsur');
				$data_input['is_mobile'] 			= true;
			}

			if(request()->has('nasabah'))
			{
				$data_input['nasabah']				= request()->get('nasabah'); 
			}

			if(request()->has('keluarga'))
			{
				$data_input['nasabah']['keluarga']	= request()->get('keluarga'); 
			}

			if(request()->has('dokumen_pelengkap'))
			{
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
			}

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

			return response()->json(['status' => 1, 'data' => $pengajuan->toArray(), 'error' => ['message' => []]]);
		} catch (Exception $e) {
			DB::rollback();
			return $this->error_response(request()->all(), $e);
		}
	}

	public function index()
	{
		try {
			//1. find pengajuan
			if(Auth::user()){
				$pkary		= PenempatanKaryawan::where('orang_id', Auth::user()['id'])->active(Carbon::now())->first();

				$pengajuan 	= new Pengajuan;

				if(!in_array(strtolower($pkary['role']), ['komisaris', 'pimpinan'])){
					$pengajuan	= $pengajuan->where('ao->nip', Auth::user()['nip']);
				}

				if(request()->has('status')){
					$pengajuan 	= $pengajuan->status(request()->get('status'));
				}

				if(request()->has('kode_kantor')){
					$pengajuan 	= $pengajuan->kantor(request()->get('kode_kantor'));
				}
			}
			else{
				$phone		= request()->get('mobile');
				$pengajuan	= Pengajuan::status('permohonan')->where('nasabah->telepon', $phone['telepon']);
			}

			$pengajuan 		= $pengajuan->paginate();
			$pengajuan->appends(request()->only('status', 'mobile'));

			return response()->json(['status' => 1, 'data' => $pengajuan, 'error' => ['message' => []]]);

		} catch (Exception $e) {
			return response()->json(['status' => 0, 'data' => [], 'error' => ['message' => []]]);
		}
	}


	private function count_distance($delta_lat, $delta_lon){
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
