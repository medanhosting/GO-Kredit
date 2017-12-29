<?php

namespace App\Http\Controllers\V2\Traits;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Jaminan;

use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Survei\Models\AssignedSurveyor;

use Thunderlabid\Manajemen\Models\Orang;

use Thunderlabid\Log\Models\Nasabah;

use App\Service\UI\UploadedGambar;
use DB, Exception, Carbon\Carbon, Validator;

/**
 * Trait tanggal
 *
 * Digunakan untuk reformat tanggal sesuai kontrak
 *
 * @package    Thunderlabid
 * @subpackage Credit
 * @author     C Mooy <chelsy@thunderlab.od>
 */
trait PengajuanTrait {
 	
	private function get_pengajuan($status, $ids = null){

		$result		= Pengajuan::status($status)->kantor(request()->get('kantor_aktif_id'))->with(['status_terakhir', 'jaminan_kendaraan', 'jaminan_tanah_bangunan', 'status_permohonan']);
		
		if($ids){
			$result = $result->whereIn('p_pengajuan.id', $ids);
		}

		if (request()->has('q_'.$status))
		{
			$cari	= request()->get('q_'.$status);
			$regexp = preg_replace("/-+/",'[^A-Za-z0-9_]+',$cari);
			$result	= $result->where(function($q)use($regexp)
			{				
				$q
				->whereRaw(DB::raw('nasabah REGEXP "'.$regexp.'"'));
			});
		}

		if (request()->has('jaminan_'.$status))
		{
			$cari	= request()->get('jaminan_'.$status);
			switch (strtolower($cari)) {
				case 'jaminan-bpkb':
					$result 	= $result->wherehas('jaminan_kendaraan', function($q){$q;});
					break;
				case 'jaminan-sertifikat':
					$result 	= $result->wherehas('jaminan_tanah_bangunan', function($q){$q;});
					break;
			}
		}

		if (request()->has('sort_'.$status)){
			$sort	= request()->get('sort_'.$status);
			switch (strtolower($sort)) {
				case 'nama-desc':
					$result 	= $result->orderby('p_pengajuan.nasabah->nama', 'desc');
					break;
				case 'tanggal-asc':
					$result 	= $result->selectraw('max(p_status.tanggal) as tanggal')->orderby('tanggal', 'asc');
					break;
				case 'tanggal-desc':
					$result 	= $result->selectraw('max(p_status.tanggal) as tanggal')->orderby('tanggal', 'desc');
					break;
				default :
					$result 	= $result->orderby('p_pengajuan.nasabah->nama', 'asc');
					break;
			}
		}else{
			$result 	= $result->orderby('p_pengajuan.nasabah->nama', 'asc');
		}

		$result		= $result->paginate(15, ['*'], $status);

		return $result;
	}

	private function store_permohonan(Pengajuan $permohonan){
		try {
			$flag = null;

			DB::BeginTransaction();

			$data_input['kode_kantor']	= request()->get('kantor_aktif_id');

			if (!$permohonan)
			{
				$data_input['is_mobile'] 						= false;
				$data_input['nasabah']['is_lama'] 				= false;
				$data_input['dokumen_pelengkap']['permohonan'] 	= true;
			}
			else
			{
				$data_input['nasabah']	= $permohonan['nasabah'];
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
				if($permohonan && count($permohonan['nasabah']['keluarga'])){
					$data_input['nasabah']['keluarga']	= $permohonan['nasabah']['keluarga'];
				}
			}

			if(request()->has('keluarga'))
			{
				$data_input['nasabah']['keluarga']	= request()->get('keluarga'); 
			}

			if(isset(request()->file('dokumen_pelengkap')['ktp']))
			{
				$data_ktp	= new UploadedGambar('ktp', request()->file('dokumen_pelengkap')['ktp']);
				$data_ktp 	= $data_ktp->handle();
				$data_input['dokumen_pelengkap']['ktp'] = $data_ktp['url'];
			}

			if(isset(request()->file('dokumen_pelengkap')['kk']))
			{
				$data_kk	= new UploadedGambar('kk', request()->file('dokumen_pelengkap')['kk']);
				$data_kk 	= $data_kk->handle();
				$data_input['dokumen_pelengkap']['kk'] = $data_kk['url'];
			}

			$permohonan->fill($data_input);
			$permohonan->save();

			if (request()->has('jaminan_kendaraan'))
			{
				foreach ($permohonan->jaminan_kendaraan as $key => $value)
				{
					$value->delete();
				}

				foreach (request()->get('jaminan_kendaraan') as $key => $value) 
				{
					if(!is_null($value['nomor_bpkb']))
					{
						$flag 	= 'jaminan_kendaraan';
						$di_jk['jenis']				= 'bpkb';
						$di_jk['tahun_perolehan']	= $value['tahun_perolehan'];
						$di_jk['nilai_jaminan']		= $value['nilai_jaminan'];

						$di_jk['dokumen_jaminan']['bpkb']['jenis']			= $value['jenis'];
						$di_jk['dokumen_jaminan']['bpkb']['merk']			= $value['merk'];
						$di_jk['dokumen_jaminan']['bpkb']['tahun']			= $value['tahun'];
						$di_jk['dokumen_jaminan']['bpkb']['nomor_bpkb']		= $value['nomor_bpkb'];
						$di_jk['dokumen_jaminan']['bpkb']['tipe']			= $value['tipe'];
						$di_jk['pengajuan_id']		= $permohonan->id;

						$jaminan	= new Jaminan;
						$jaminan->fill($di_jk);
						$jaminan->save();
					}
				}
			}

			if (request()->has('jaminan_tanah_bangunan'))
			{
				foreach ($permohonan->jaminan_tanah_bangunan as $key => $value)
				{
					$value->delete();
				}

				foreach (request()->get('jaminan_tanah_bangunan') as $key => $value) 
				{
					if(!is_null($value['nomor_sertifikat']))
					{
						$flag 	= 'jaminan_tanah_bangunan';
						$di_jtb['jenis']				= $value['jenis'];
						$di_jtb['tahun_perolehan']		= $value['tahun_perolehan'];
						$di_jtb['nilai_jaminan']		= $value['nilai_jaminan'];

						$di_jtb['dokumen_jaminan'][$value['jenis']]['nomor_sertifikat']	= $value['nomor_sertifikat'];
						$di_jtb['dokumen_jaminan'][$value['jenis']]['tipe']				= $value['tipe'];

						$di_jtb['dokumen_jaminan'][$value['jenis']]['luas_tanah']		= $value['luas_tanah'];
						
						if($value['tipe']=='tanah_dan_bangunan'){
							$di_jtb['dokumen_jaminan'][$value['jenis']]['luas_bangunan']= $value['luas_bangunan'];
						}
						if($value['jenis']=='shgb'){
							$di_jtb['dokumen_jaminan'][$value['jenis']]['masa_berlaku_sertifikat']	= $value['masa_berlaku_sertifikat'];
						}
						$di_jtb['dokumen_jaminan'][$value['jenis']]['alamat']			= $value['alamat'];
						$di_jtb['pengajuan_id']			= $permohonan->id;

						$jaminan	= new Jaminan;
						$jaminan->fill($di_jtb);
						$jaminan->save();
					}
				}
			}
	
			DB::commit();

			return $permohonan;
		} catch (Exception $e) {
			DB::rollback();

			foreach ($e->getMessage()->toarray() as $k => $v) {
				$exp 	= explode('.', $k);

				if(str_is('dokumen_jaminan.bpkb.*', $k) || $flag == 'jaminan_kendaraan')
				{
					if(count($exp) ==4)
					{
						$msg['jaminan_kendaraan['.$key.']['.$exp[2].']['.$exp[3].']'] = $v;
					}
					elseif(count($exp) ==3)
					{
						$msg['jaminan_kendaraan['.$key.']['.$exp[2].']'] = $v;
					}
					elseif(count($exp) ==2)
					{
						$msg['jaminan_kendaraan['.$key.']['.$exp[1].']'] = $v;
					}
					else
					{
						$msg['jaminan_kendaraan['.$key.']['.$exp[0].']'] = $v;
					}
				}
				
				elseif(str_is('dokumen_jaminan.shm.*', $k) || str_is('dokumen_jaminan.shgb.*', $k) || $flag == 'jaminan_tanah_bangunan')
				{
					if(count($exp) ==4)
					{
						$msg['jaminan_tanah_bangunan['.$key.']['.$exp[2].']['.$exp[3].']'] = $v;
					}
					elseif(count($exp) ==3)
					{
						$msg['jaminan_tanah_bangunan['.$key.']['.$exp[2].']'] = $v;
					}
					elseif(count($exp) ==2)
					{
						$msg['jaminan_tanah_bangunan['.$key.']['.$exp[1].']'] = $v;
					}
					else
					{
						$msg['jaminan_tanah_bangunan['.$key.']['.$exp[0].']'] = $v;
					}
				}
				else
				{
					if(count($exp) ==4)
					{
						$msg[$exp[1].'['.$exp[2].']['.$exp[3].']'] 	= $v;
					}
					elseif(count($exp) ==3)
					{
						$msg[$exp[0].'['.$exp[1].']['.$exp[2].']'] 	= $v;
					}
					elseif(count($exp) ==2)
					{
						$msg[$exp[0].'['.$exp[1].']'] 	= $v;
					}
					else
					{
						$msg[$exp[0]] = $v;
					}
				}
			}

			return $msg;
		}
	}

	private function assign_surveyor($permohonan){
		try {
			DB::BeginTransaction();

			$survei 				= new Survei;
			$survei->pengajuan_id 	= $permohonan['id'];
			$survei->tanggal 		= Carbon::now()->format('d/m/Y H:i');
			$survei->kode_kantor 	= $permohonan['kode_kantor'];
			$survei->save();
			foreach (request()->get('surveyor')['nip'] as $k => $v) {
				$assign_survei 				= new AssignedSurveyor;
				$assign_survei->survei_id 	= $survei->id;
				$assign_survei->nip			= $v;
				$assign_survei->nama		= Orang::where('nip', $v)->first()['nama'];
				$assign_survei->save();
			}

			DB::commit();
			return $survei;
		} catch (Exception $e) {
			DB::rollback();
			return $e;
		}
	}

	private function checker_permohonan($permohonan) 
	{
		$checker 	= [];
		$complete 	= 0;
		$total 		= 3;

		//CHECKER KREDIT
		if (is_null($permohonan['pokok_pinjaman'])) {
			$checker['kredit']	= false;
			$complete 			= 0;
		} else {
			$checker['kredit']	= true;
			$complete 			= 3;
		}

		//CHECKER NASABAH
		$rule_n 	= Nasabah::rule_of_valid();
		$total 		= $total + count($rule_n);

		if (count($permohonan['nasabah'])) {
			$validator 	= Validator::make($permohonan['nasabah'], $rule_n);

			if ($validator->fails()) {
				$complete 				= $complete + (count($rule_n) - count($validator->messages()));
				$checker['nasabah'] 	= false;
			} else {
				$complete 				= $complete + count($rule_n);
				$checker['nasabah'] 	= true;
			}
		} else {
			$checker['nasabah'] 		= false;
		}

		//CHECKER KELUARGA
		$rule_k 		= Nasabah::rule_of_valid_family();

		if (count($permohonan['nasabah']['keluarga'])) {
			foreach ($permohonan['nasabah']['keluarga'] as $kk => $kv) {
				$total 		= $total + count($kv);
				$validator 	= Validator::make($kv, $rule_k);

				if ($validator->fails()) {
					$complete 				= $complete + (count($kv) - count($validator->messages()));
					$checker['keluarga'] 	= false;
				} else {
					$complete 				= $complete + count($kv);
					$checker['keluarga'] 	= true;
				}
			}
		} else {
			$total 						= $total + count($rule_k);
			$checker['keluarga'] 		= false;
		}

		//CHECKER JAMINAN
		$flag_jam 			= true;

		if (count($permohonan['jaminan_kendaraan'])) {
			foreach ($permohonan['jaminan_kendaraan'] as $k => $v) {
				$c_col 		= Jaminan::rule_of_valid_jaminan_bpkb();
				$total 		= $total + count($c_col);
				$validator 	= Validator::make($v['dokumen_jaminan'][$v['jenis']], $c_col);

				if ($validator->fails()) {
					$complete 				= $complete + (count($c_col) - count($validator->messages()));
					$checker['jaminan'] 	= false;
					$permohonan['jaminan_kendaraan'][$k]['is_lengkap'] = false;
				} else {
					$complete 				= $complete + count($c_col);

					if (is_null($checker['jaminan']) || $checker['jaminan']) {
						$checker['jaminan'] 	= true;
					}

					$permohonan['jaminan_kendaraan'][$k]['is_lengkap'] = true;
				}

				if (!$v['dokumen_jaminan'][$v['jenis']]['is_lama']) {
					$flag_jam 	= false;
				}
			}
		}

		if (count($permohonan['jaminan_tanah_bangunan'])) {
			foreach ($permohonan['jaminan_tanah_bangunan'] as $k => $v) {
				$c_col 		= Jaminan::rule_of_valid_jaminan_sertifikat($v['jenis'], $v['dokumen_jaminan'][$v['jenis']]['jenis']);
				$total 		= $total + count($c_col);
				$validator 	= Validator::make($v['dokumen_jaminan'][$v['jenis']], $c_col);

				if ($validator->fails()) {
					$complete 				= $complete + (count($c_col) - count($validator->messages()));
					$checker['jaminan'] 	= false;
					$permohonan['jaminan_tanah_bangunan'][$k]['is_lengkap'] = false;
				} else {
					$complete 				= $complete + count($c_col);

					if(is_null($checker['jaminan']) || $checker['jaminan']) {
						$checker['jaminan'] 	= true;
					}
					$permohonan['jaminan_tanah_bangunan'][$k]['is_lengkap'] = true;
				}

				if(!$v['dokumen_jaminan'][$v['jenis']]['is_lama']) {
					$flag_jam 	= false;
				}
			}
		}

		if (!count($permohonan['jaminan_kendaraan']) && !count($permohonan['jaminan_tanah_bangunan'])) {
			$total 		= $total + 1;
		}

		$percentage 	= floor(($complete / max($total, 1)) * 100);

		view()->share('checker', $checker);
		view()->share('percentage', $percentage);
	}
}