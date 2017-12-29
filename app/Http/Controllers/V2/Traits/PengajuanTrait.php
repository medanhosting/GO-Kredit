<?php

namespace App\Http\Controllers\V2\Traits;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Jaminan;
use Thunderlabid\Pengajuan\Models\Analisa;
use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Pengajuan\Models\Status;

use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Survei\Models\SurveiDetail;
use Thunderlabid\Survei\Models\AssignedSurveyor;

use Thunderlabid\Manajemen\Models\Orang;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use Thunderlabid\Log\Models\Nasabah;

use App\Service\UI\UploadedGambar;
use DB, Exception, Carbon\Carbon, Validator, Auth;

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

	private function store_survei($permohonan){
		try {
			$survei 			= Survei::where('pengajuan_id', $permohonan['id'])->wherehas('pengajuan', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->with(['character', 'condition', 'capacity', 'capital', 'collateral'])->orderby('tanggal', 'desc')->first();

			if(!$survei)
			{
				throw new Exception("Dokumen ini tidak diijinkan untuk survei", 1);
			}
			if(request()->has('tanggal_survei'))
			{
				$survei->tanggal 	= request()->get('tanggal_survei');
				$survei->save();
			}

			if(request()->has('character'))
			{
				$character 			= SurveiDetail::where('survei_id', $survei['id'])->where('jenis', 'character')->first();
				if(!$character){
					$character 		= new SurveiDetail;
				}

				$character->survei_id 		= $survei['id'];
				$character->jenis 			= 'character';
				$character->dokumen_survei 	= request()->only('character');
				$character->save();
			}
	
			if(request()->has('condition'))
			{
				$condition 					= SurveiDetail::where('survei_id', $survei['id'])->where('jenis', 'condition')->first();
				if(!$condition){
					$condition 				= new SurveiDetail;
				}

				$condition->survei_id 		= $survei['id'];
				$condition->jenis 			= 'condition';
				$ds_condition 				= request()->only('condition');
				$ds_condition['condition']['pekerjaan'] 	= $survei->pengajuan->nasabah['pekerjaan'];
				$ds_condition 				= array_map('array_filter', $ds_condition);;
				$condition->dokumen_survei 	= $ds_condition;
				$condition->save();
			}

			if(request()->has('capacity'))
			{
				$capacity 					= SurveiDetail::where('survei_id', $survei['id'])->where('jenis', 'capacity')->first();
				if(!$capacity){
					$capacity 				= new SurveiDetail;
				}

				$capacity->survei_id 		= $survei['id'];
				$capacity->jenis 			= 'capacity';
				$ds_capacity 			 	= request()->only('capacity');
				$sp 						= strtolower($ds_capacity['capacity']['status_pernikahan']);

				if(str_is($sp, 'tk')){
					$ds_capacity['capacity']['tanggungan_keluarga'] = $this->formatMoneyTo(1500000);
				}elseif(str_is($sp, 'k')){
					$ds_capacity['capacity']['tanggungan_keluarga'] = $this->formatMoneyTo(3000000);
				}else{
					$anak 					= str_replace('k-', '', $sp) * 1;
					$ds_capacity['capacity']['tanggungan_keluarga']	= $this->formatMoneyTo(3000000 + ($anak * 1250000));
				}
				$ds_capacity['capacity']['pekerjaan'] 	= $survei->pengajuan->nasabah['pekerjaan'];

				$capacity->dokumen_survei 	= $ds_capacity;
				$capacity->save();
			}

			if(request()->has('capital'))
			{
				$capital 					= SurveiDetail::where('survei_id', $survei['id'])->where('jenis', 'capital')->first();
				if(!$capital){
					$capital 				= new SurveiDetail;
				}

				$capital->survei_id 		= $survei['id'];
				$capital->jenis 			= 'capital';
				$ds_capital 				= request()->only('capital');
				$ds_capital['capital']['pekerjaan'] 	= $survei->pengajuan->nasabah['pekerjaan'];
				$ds_capital 				= array_map('array_filter', $ds_capital);;
				$capital->dokumen_survei 	= $ds_capital;
				$capital->save();
			}

			if(request()->has('collateral'))
			{
				$collateral 				= SurveiDetail::where('survei_id', $survei['id'])->where('id', request()->get('survei_detail_id'))->where('jenis', 'collateral')->first();
				if(!$collateral){
					throw new Exception("Jaminan tidak terdaftar!", 1);
				}

				$collateral->survei_id 		= $survei['id'];
				$collateral->jenis 			= 'collateral';
				$key 	= key(request()->get('collateral')[request()->get('survei_detail_id')]);

				$ds_all = $collateral->dokumen_survei;
				$ds		= array_merge($collateral->dokumen_survei['collateral'][$key], request()->get('collateral')[request()->get('survei_detail_id')][$key]);

				$ds_all['collateral'][$key] = $ds;

				if($key=='bpkb')
				{
					$ds_all['collateral'][$key]['harga_taksasi']	= $this->formatMoneyTo($this->formatMoneyFrom($ds_all['collateral'][$key]['nilai_kendaraan']) * ($ds_all['collateral'][$key]['persentasi_taksasi']/100));
					$ds_all['collateral'][$key]['harga_bank']		= $this->formatMoneyTo($this->formatMoneyFrom($ds_all['collateral'][$key]['nilai_kendaraan']) * ($ds_all['collateral'][$key]['persentasi_bank']/100));
				}else{
					$nilai_t 	= $this->formatMoneyFrom($ds_all['collateral'][$key]['nilai_tanah']); 
					if(isset($ds_all['collateral'][$key]['nilai_bangunan'])){
						$nilai_b 	= $this->formatMoneyFrom($ds_all['collateral'][$key]['nilai_bangunan']); 
					}else{
						$nilai_b 	= 0;
					}
					$ds_all['collateral'][$key]['harga_taksasi']	= $this->formatMoneyTo(($nilai_b + $nilai_t) * ($ds_all['collateral'][$key]['persentasi_taksasi']/100));
				}
				$collateral->dokumen_survei = $ds_all;
				$collateral->save();
			}

			return $permohonan;
		} catch (Exception $e) {
			foreach ($e->getMessage()->toarray() as $k => $v) {
				$exp 	= explode('.', $k);

				if(request()->has('collateral'))
				{
					if(count($exp) ==4)
					{
						$msg[$exp[1].'['.request()->get('survei_detail_id').']['.$exp[2].']['.$exp[3].']'] 	= $v;
					}
					elseif(count($exp) ==3)
					{
						$msg[$exp[1].'['.request()->get('survei_detail_id').']['.$exp[2].']'] 	= $v;
					}
					elseif(count($exp) ==2)
					{
						$msg[$exp[1].'['.request()->get('survei_detail_id').']'] 	= $v;
					}
					else
					{
						$msg[$exp[0].'['.request()->get('survei_detail_id').']'] 	= $v;
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
						$msg[$exp[1].'['.$exp[2].']'] 	= $v;
					}
					elseif(count($exp) ==2)
					{
						$msg[$exp[1]] 	= $v;
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

	private function store_analisa($permohonan){
		try {
			$analisa 		= Analisa::where('pengajuan_id', $permohonan['id'])->wherehas('pengajuan', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->orderby('tanggal', 'desc')->first();

			if(!$analisa)
			{
				$analisa 	= new Analisa;
			}

			$data_input 				= request()->all();
			$data_input['analis']		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
			$data_input['pengajuan_id']	= $permohonan['id'];
			$data_input['limit_jangka_waktu']	= $data_input['jangka_waktu'];
			$data_input['limit_angsuran']		= $data_input['kredit_diusulkan'];

			$analisa->fill($data_input);
			$analisa->save();

			return $permohonan;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	private function store_putusan($permohonan){
		try {
			$putusan 			= Putusan::where('pengajuan_id', $permohonan['id'])->wherehas('pengajuan', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->orderby('tanggal', 'desc')->first();

			if(!$putusan)
			{
				$putusan 		= new Putusan;
			}

			if(!request()->has('checklists'))
			{
				$data_input 					= request()->only('tanggal', 'plafon_pinjaman', 'suku_bunga', 'jangka_waktu', 'perc_provisi', 'administrasi', 'legal', 'putusan', 'catatan');

				$data_input['pembuat_keputusan']= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
				$data_input['pengajuan_id']		= $permohonan['id'];
				$data_input['provisi']			= $this->formatMoneyTo(($this->formatMoneyFrom($data_input['plafon_pinjaman']) * $data_input['perc_provisi'])/100);

				$data_input['is_baru']			= true;
				$r_nasabah 		= $this->riwayat_kredit_nasabah($putusan['pengajuan']['nasabah']['nik'], $permohonan['id']);
				if(count($r_nasabah))
				{
					$data_input['is_baru']		= false;
				}
			}

			if(request()->has('checklists'))
			{
				$data_input['checklists'] 		= request()->get('checklists');
			}

			$putusan->fill($data_input);
			$putusan->save();

			return $permohonan;

		} catch (Exception $e) {
			return $e->getMessage();
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

	private function assign_analis($permohonan)
	{
		try {
			if(is_null(request()->get('analis')['nip'])){
				throw new Exception("Analis Belum dipilih", 1);
			}

			DB::BeginTransaction();

			$analis['nip']			= request()->get('analis')['nip'];
			$analis['nama']			= Orang::where('nip', request()->get('analis')['nip'])->first()['nama'];
			if(is_null($analis['nama'])){
				throw new Exception("Analis Tidak Valid", 1);
			}

			$status 				= new Status;
			$status->pengajuan_id 	= $permohonan['id'];
			$status->status 		= 'analisa';
			$status->progress 		= 'perlu';
			$status->karyawan 		= $analis;
			$status->tanggal 		= Carbon::now()->format('d/m/Y H:i');
			$status->save();

			DB::commit();
			return $status;
		} catch (Exception $e) {
			DB::rollback();
			return $e->getMessage();
		}
 	}

	private function assign_komite_putusan($permohonan){
		try {

			DB::BeginTransaction();

			//pimpinan
			$kredit_diusulkan 	= $this->formatMoneyFrom($permohonan['analisa']['kredit_diusulkan']);
			
			if($kredit_diusulkan > 10000000)
			{
				$pimpinan 		= PenempatanKaryawan::where('kantor_id', request()->get('kantor_aktif_id'))->active(Carbon::now())->where('role', 'komisaris')->first();
			}
			else
			{
				$pimpinan 		= PenempatanKaryawan::where('kantor_id', request()->get('kantor_aktif_id'))->active(Carbon::now())->where('role', 'pimpinan')->first();
			}

			if(!$pimpinan)
			{
				throw new Exception("Tidak ada data pimpinan", 1);
			}

			$pk['nip']			= $pimpinan['orang']['nip'];
			$pk['nama']			= $pimpinan['orang']['nama'];

			$status 				= new Status;
			$status->pengajuan_id 	= $permohonan['id'];
			$status->status 		= 'putusan';
			$status->progress 		= 'perlu';
			$status->karyawan 		= $pk;
			$status->tanggal 		= Carbon::now()->format('d/m/Y H:i');
			$status->save();

			DB::commit();

			return $status;
		} catch (Exception $e) {
			DB::rollback();
			return $e->getMessage();
		}
	}

	private function assign_realisasi($permohonan){
		try {
			DB::BeginTransaction();

			$pk['nip']			= Auth::user()['nip'];
			$pk['nama']			= Auth::user()['nama'];

			$status 				= new Status;
			$status->pengajuan_id 	= $permohonan['id'];
			$status->status 		= $permohonan->putusan['putusan'];
			$status->progress 		= 'sudah';
			$status->karyawan 		= $pk;
			$status->tanggal 		= Carbon::now()->format('d/m/Y H:i');
			$status->save();

			DB::commit();
			return $status;
		} catch (Exception $e) {
			DB::rollback();
			return $e->getMessage();
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

	private function checker_survei($survei){
		//checker character
		$c_char 	= SurveiDetail::rule_of_valid_character();
		$total 		= $total + count($c_char);

		if(count($survei['character']))
		{
			$validator 	= Validator::make($survei['character']['dokumen_survei']['character'], $c_char);
			if ($validator->fails())
			{
				$complete 				= $complete + (count($c_char) - count($validator->messages()));
				$checker['character'] 	= false;
			}
			else
			{
				$complete 				= $complete + count($c_char);
				$checker['character'] 	= true;
			}
		}
		else
		{
			$checker['character'] 		= false;
		}

		//checker condition
		$c_cond 	= SurveiDetail::rule_of_valid_condition();
		$total 		= $total + count($c_cond);

		if(count($survei['condition']))
		{
			$validator 	= Validator::make($survei['condition']['dokumen_survei']['condition'], $c_cond);
			if ($validator->fails())
			{
				$complete 				= $complete + (count($c_cond) - count($validator->messages()));
				$checker['condition'] 	= false;
			}
			else
			{
				$complete 				= $complete + count($c_cond);
				$checker['condition'] 	= true;
			}
		}
		else
		{
			$checker['condition'] 		= false;
		}

		//checker capital
		$c_capi 	= SurveiDetail::rule_of_valid_capital();
		$total 		= $total + count($c_capi);

		if(count($survei['capital']))
		{
			$validator 	= Validator::make($survei['capital']['dokumen_survei']['capital'], $c_capi);
			if ($validator->fails())
			{
				$complete 				= $complete + (count($c_capi) - count($validator->messages()));
				$checker['capital'] 	= false;
			}
			else
			{
				$complete 				= $complete + count($c_capi);
				$checker['capital'] 	= true;
			}
		}
		else
		{
			$checker['capital'] 		= false;
		}

		//checker capacity
		$c_capa 	= SurveiDetail::rule_of_valid_capacity();
		$total 		= $total + count($c_capa);

		if(count($survei['capacity']))
		{
			$validator 	= Validator::make($survei['capacity']['dokumen_survei']['capacity'], $c_capa);
			if ($validator->fails())
			{
				$complete 				= $complete + (count($c_capa) - count($validator->messages()));
				$checker['capacity'] 	= false;
			}
			else
			{
				$complete 				= $complete + count($c_capa);
				$checker['capacity'] 	= true;
			}
		}
		else
		{
			$checker['capacity'] 		= false;
		}

		//checker collateral
		if(count($survei['jaminan_kendaraan'])){
			foreach ($survei['jaminan_kendaraan'] as $k => $v) {
				$c_col 		= SurveiDetail::rule_of_valid_collateral_bpkb();
				$total 		= $total + count($c_col);
				$v['dokumen_survei']['collateral'][$v['dokumen_survei']['collateral']['jenis']]['has_foto']	= $v['has_foto'];

				$validator 	= Validator::make($v['dokumen_survei']['collateral']['bpkb'], $c_col);
				if ($validator->fails())
				{
					$complete 				= $complete + (count($c_col) - count($validator->messages()));
					$checker['collateral'] 	= false;
					$survei['jaminan_kendaraan'][$k]['is_lengkap'] = false;
				}
				else
				{
					$complete 				= $complete + count($c_col);
					if(is_null($checker['collateral']) || $checker['collateral'])
					{
						$checker['collateral'] 	= true;
					}
					$survei['jaminan_kendaraan'][$k]['is_lengkap'] = true;
				}
			}
		}

		if(count($survei['jaminan_tanah_bangunan'])){
			foreach ($survei['jaminan_tanah_bangunan'] as $k => $v) {
				$c_col 		= SurveiDetail::rule_of_valid_collateral_sertifikat($v['dokumen_survei']['collateral']['jenis'], $v['dokumen_survei']['collateral'][$v['dokumen_survei']['collateral']['jenis']]['tipe']);
				$total 		= $total + count($c_col);

				$v['dokumen_survei']['collateral'][$v['dokumen_survei']['collateral']['jenis']]['has_foto']	= $v['has_foto'];
				$validator 	= Validator::make($v['dokumen_survei']['collateral'][$v['dokumen_survei']['collateral']['jenis']], $c_col);
				if ($validator->fails())
				{
					$complete 				= $complete + (count($c_col) - count($validator->messages()));
					$checker['collateral'] 	= false;
					$survei['jaminan_tanah_bangunan'][$k]['is_lengkap'] = false;
				}
				else
				{
					$complete 				= $complete + count($c_col);
					if(is_null($checker['collateral']) || $checker['collateral'])
					{
						$checker['collateral'] 	= true;
					}
					$survei['jaminan_tanah_bangunan'][$k]['is_lengkap'] = true;
				}
			}
		}
		
		$percentage 	= floor(($complete / max($total, 1)) * 100);

		view()->share('checker', $checker);
		view()->share('percentage', $percentage);			
	}

	private function checker_analisa($analisa){
		$checker 	= [];
		$complete 	= 0;
		$total 		= 0;

		//checker character
		$r_analisa 	= Analisa::rule_of_valid();
		$total 		= $total + count($r_analisa);

		if($analisa)
		{
			$validator 	= Validator::make($analisa->toArray(), $r_analisa);
			if ($validator->fails())
			{
				$complete 				= $complete + (count($r_analisa) - count($validator->messages()));
				$checker['analisa'] 	= false;
			}
			else
			{
				$complete 				= $complete + count($r_analisa);
				$checker['analisa'] 	= true;
			}
		}
		else
		{
			$checker['analisa'] 		= false;
		}

		$percentage 	= floor(($complete / max($total, 1)) * 100);

		view()->share('checker', $checker);
		view()->share('percentage', $percentage);	
	}


	private function checker_putusan($putusan){
		$checker 	= [];
		$complete 	= 0;
		$total 		= 0;

		//checker character
		$r_putusan 	= Putusan::rule_of_valid();
		$total 		= $total + count($r_putusan);

		if($putusan)
		{
			$validator 	= Validator::make($putusan->toArray(), $r_putusan);
			if ($validator->fails())
			{
				$complete 				= $complete + (count($r_putusan) - count($validator->messages()));
				$checker['putusan'] 	= false;
			}
			else
			{
				$complete 				= $complete + count($r_putusan);
				$checker['putusan'] 	= true;
			}
		}
		else
		{
			$checker['putusan'] 		= false;
		}

		$percentage 	= floor(($complete / max($total, 1)) * 100);

		view()->share('checker', $checker);
		view()->share('percentage', $percentage);	
	}
}