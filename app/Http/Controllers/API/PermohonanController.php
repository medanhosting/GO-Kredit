<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller as BaseController;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Jaminan;
use Thunderlabid\Manajemen\Models\Kantor;

use Exception, Response;

class PermohonanController extends BaseController
{
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
			$data_jaminan		= request()->get('jaminan');

			//!!!!!!!CHECK AO!!!!!!!//
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

			return Response::json(['Sukses']);
		} catch (Exception $e) {
			return Response::json($e->getMessage());
		}

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
