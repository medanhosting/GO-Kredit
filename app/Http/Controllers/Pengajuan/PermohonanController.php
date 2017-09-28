<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Jaminan;

use Thunderlabid\Log\Models\Nasabah;

use App\Http\Service\UI\UploadedGambar;

use Exception, Session, MessageBag, Validator, DB;

class PermohonanController extends Controller
{
	protected $view_dir = 'pengajuan.permohonan.';

	public function __construct()
	{
		parent::__construct();

		$this->middleware('scope:permohonan');
		
		$this->middleware('required_password')->only('destroy');
	}

	public function index ($status = 'permohonan') 
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
							->orwhere('p_pengajuan.id', 'like', '%'.$cari.'%');
			});
		}

		$pengajuan 				= $pengajuan->orderby('p_pengajuan.created_at', $urut)->paginate();

		view()->share('pengajuan', $pengajuan);
		view()->share('status', $status);
		view()->share('order', $order);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view($this->view_dir . 'index');
		return $this->layout;
	}

	public function show($id, $status = 'permohonan')
	{
		try {
			$permohonan		= Pengajuan::where('p_pengajuan.id', $id)->status($status)->kantor(request()->get('kantor_aktif_id'))->with('jaminan_kendaraan', 'jaminan_tanah_bangunan', 'riwayat_status', 'status_terakhir')->first();

			$breadcrumb 	= [
				[
					'title'	=> $status,
					'route' => route('pengajuan.permohonan.index', ['status' => $status, 'kantor_aktif_id' => request()->get('kantor_aktif_id')])
				], 
				[
					'title'	=> $id,
					'route' => route('pengajuan.permohonan.index', ['status' => $status, 'id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')])
				]
			];

			if (!$permohonan)
			{
				throw new Exception("Data tidak ada!", 1);
			}

			$checker 	= [];
			//checker nasabah
			$rule_n 	= Nasabah::rule_of_valid();
			$total 		= count($rule_n);
			$complete 	= 0;

			$validator 	= Validator::make($permohonan['nasabah'], $rule_n);
			if ($validator->fails())
			{
				$complete 				= $complete + $total - count($validator->messages());
				$checker['nasabah'] 	= false;
			}
			else
			{
				$complete 				= $complete + count($rule_n);
				$checker['nasabah'] 	= true;
			}

			//checker keluarga
			$rule_k 		= Nasabah::rule_of_valid_family();
			if(count($permohonan['nasabah']['keluarga']))
			{
				foreach ($permohonan['nasabah']['keluarga'] as $kk => $kv) {
					$total 		= $total + count($kv);
					$validator 	= Validator::make($kv, $rule_k);
					if ($validator->fails())
					{
						$complete 				= $complete + $total - count($validator->messages());
						$checker['keluarga'] 	= false;
					}
					else
					{
						$complete 				= $complete + count($kv);
						$checker['keluarga'] 	= true;
					}
				}
			}
			else
			{
				$total 						= $total + count($rule_k);
				$checker['keluarga'] 		= false;
			}

			$total 		= $total + 1;
			//checker jaminan_kendaraan
			if(count($permohonan['jaminan_kendaraan']) || count($permohonan['jaminan_tanah_bangunan']))
			{
				$complete 			= $complete + 1;
				$checker['jaminan']	= true;
			}
			else
			{
				$checker['jaminan']	= false;
			}

			$percentage 	= floor(($complete / max($total, 1)) * 100);

			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
			view()->share('breadcrumb', $breadcrumb);
			view()->share('status', $status);
			$this->variable_to_view();

			$this->layout->pages 	= view('pengajuan.permohonan.show', compact('checker', 'permohonan', 'percentage'));
			return $this->layout;

		} catch (Exception $e) {
			return redirect(route('pengajuan.permohonan.index', ['status' => $status, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
		}
	}

	public function create ($id = null)
	{
		try {
			$permohonan 			= Pengajuan::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->with('jaminan')->first();
			if (!$permohonan && is_null($id))
			{
				$permohonan 		= new Pengajuan;
			}
			elseif(!$permohonan)
			{
				throw new Exception("Data tidak ada, Silahkan buat permohonan baru.", 1);
			}

			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
			view()->share('hari_ini', \Carbon\Carbon::now()->format('d/m/Y'));
			view()->share('permohonan', $permohonan);
			$this->variable_to_view();

			$this->layout->pages 	= view($this->view_dir . 'create');
			return $this->layout;
		} catch (Exception $e) {
			return redirect(route('permohonan.create', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
		}
	}

	public function store ($id = null)
	{
		try {

			DB::BeginTransaction();

			// $data_input 				= request()->all();
			$data_input['kode_kantor']	= request()->get('kantor_aktif_id');

			$permohonan 				= Pengajuan::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->with('jaminan')->first();
			if (!$permohonan)
			{
				$data_input['is_mobile'] 	= false;
				$permohonan 				= new Pengajuan;
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

			if (request()->has('jaminan_tanah_bangunan'))
			{
				foreach ($permohonan->jaminan_tanah_bangunan as $key => $value)
				{
					$value->delete();
				}

				foreach (request()->get('jaminan_tanah_bangunan') as $key => $value) 
				{
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
	
			DB::commit();

			return redirect(route($this->view_dir . 'show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]));
		} catch (Exception $e) {
			DB::rollback();

			if (!is_null($id))
			{
				return redirect(route($this->view_dir . 'show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
			}

			$errors = new MessageBag();
			return redirect(route($this->view_dir . 'create', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage())->withInput();
		}
	}

	public function edit($id)
	{
		return $this->create($id);
	}

	public function update($id)
	{
		return $this->store($id);
	}

	public function destroy($id)
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

			return redirect(route('permohonan.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]));
		} catch (Exception $e) {
			return redirect(route('permohonan.show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
		}
	}

	private function variable_to_view () 
	{
		$jenis_pekerjaan	= [
			'belum_bekerja'		=> 'Belum / Tidak Bekerja',
			'karyawan_swasta'	=> 'Karyawan Swasta',
			'nelayan'			=> 'Nelayan',
			'pegawai_negeri'	=> 'Pegawai Negeri',
			'petani'			=> 'Petani',
			'polri'				=> 'Polri',
			'wiraswasta'		=> 'Wiraswasta',
			'lain_lain'			=> 'Lainnya'
		];

		$jenis_kendaraan 	= [
			'roda_2'		=> 'roda 2',
			'roda_3'		=> 'roda 3',
			'roda_4'		=> 'roda 4',
			'roda_6'		=> 'roda 6'
		];

		$jenis_sertifikat	= [
			'shm'			=> 'SHM',
			'shgb'			=> 'SHGB'
		];

		$tipe_sertifikat	= [
			'tanah'				=> 'Tanah',
			'tanah_dan_bangunan'=> 'Tanah & Bangunan'
		];

		$status_perkawinan = [
			'belum_kawin'	=> 'Belum Kawin',
			'kawin'			=> 'Kawin',
			'cerai'			=> 'Cerai',
			'cerai_mati'	=> 'Cerai Mati'
		];

		view()->share('jenis_pekerjaan', $jenis_pekerjaan);
		view()->share('jenis_kendaraan', $jenis_kendaraan);
		view()->share('jenis_sertifikat', $jenis_sertifikat);
		view()->share('tipe_sertifikat', $tipe_sertifikat);
		view()->share('status_perkawinan', $status_perkawinan);
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
