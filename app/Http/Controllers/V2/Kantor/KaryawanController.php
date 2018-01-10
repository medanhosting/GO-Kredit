<?php

namespace App\Http\Controllers\V2\Kantor;

use App\Http\Controllers\Controller;

use Thunderlabid\Manajemen\Models\Orang;
use Thunderlabid\Manajemen\Models\Kantor;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use Illuminate\Support\Str;
use Exception, DB, Auth, Carbon\Carbon;

class KaryawanController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->middleware('scope:karyawan');
	}

	public function index () 
	{
		$kantor 		= Kantor::get(['id', 'nama']);
		$karyawan		= new Orang;

		if(request()->has('q')){
			$karyawan 	= $karyawan->where('nama', 'like', '%'.request()->get('q').'%');
		}

		if(request()->has('kantor') && !str_is(request()->get('kantor'), 'semua')){
			$karyawan 	= $karyawan->wherehas('penempatanaktif', function($q){$q->where('kantor_id', request()->get('kantor'));});
		}

		if(request()->has('sort')){
			switch (strtolower(request()->get('sort'))) {
				case 'nama-asc':
					$karyawan 	= $karyawan->orderby('nama', 'asc');
					break;
				default:
					$karyawan 	= $karyawan->orderby('nama', 'desc');
					break;
			}
		}else{
			$karyawan 	= $karyawan->orderby('nama', 'asc');
		}

		$karyawan 		= $karyawan->with(['penempatanaktif', 'penempatanaktif.kantor'])->paginate(15, ['*'], 'karyawan');

		view()->share('is_karyawan_tab', 'show active');

		view()->share('active_submenu', 'karyawan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kantor.karyawan.index', compact('karyawan', 'kantor'));
		return $this->layout;
	}

	public function show($id)
	{
		return $this->create($id);
	}

	public function create ($id = null) 
	{
		$karyawan 				= Orang::findornew($id);
		
		view()->share('active_submenu', 'karyawan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kantor.karyawan.create', compact('karyawan'));
		return $this->layout;
	}

	public function store($id = null) 
	{
		try {
			if(is_null($id))
			{
				$orang	= request()->only('nama', 'email', 'telepon', 'telepon', 'alamat', 'password', 'confirm_password');
			}
			else
			{
				$orang	= request()->only('nama', 'email', 'telepon', 'telepon', 'alamat');
			}
			$penempatan 		= request()->get('kantor');

			DB::beginTransaction();
			$orang_simpan 		= Orang::findornew($id);
			$orang_simpan->fill($orang);
			$orang_simpan->save();

			if(is_null($id))
			{
				$penempatan['kantor_id']= request()->get('kantor_id');
				$penempatan['orang_id']	= $orang_simpan['id'];
				if(in_array('keputusan', $penempatan['scopes']) && $penempatan['role']=='pimpinan')
				{
					$penempatan['policies']	= ['keputusan' => ['max:10000000']];
				}
				else
				{
					$penempatan['policies']	= [];
				}
				$penempatan_simpan 		= new PenempatanKaryawan;
				$penempatan_simpan->fill($penempatan);
				$penempatan_simpan->save();
			}
			DB::commit();

			return redirect()->route('karyawan.show', ['id' => $orang_simpan['id'], 'kantor_aktif_id' => $this->kantor_aktif['id']]);
		} catch (Exception $e) {
			DB::rollback();
			return redirect()->back()->withInput()->withErrors($e->getMessage());
		}
	}

	public function edit($id)
	{
		return $this->create($id);
	}

	public function update($id)
	{
		if(request()->has('mode'))
		{
			switch (request()->get('mode')) {
				case 'mutasi':
					return $this->pindah(request()->get('penempatan_id'));
					break;
				case 'resign':
					return $this->resign(request()->get('penempatan_id'));
					break;
				default:
					return $this->penempatan_baru($id);
					break;
			}
		}
		return $this->store($id);
	}

	private function pindah($penempatan_id)
	{
		try {
			DB::beginTransaction();
			$penempatan 	= PenempatanKaryawan::findorfail($penempatan_id);
			$penempatan->tanggal_keluar 	= request()->get('tanggal_pindah');
			$penempatan->save();

			$penempatan_baru 	= new PenempatanKaryawan;
			$penempatan_baru->kantor_id 	= request()->get('kantor_id');
			$penempatan_baru->orang_id 		= $penempatan->orang_id;
			$penempatan_baru->role 			= $penempatan->role;
			$penempatan_baru->scopes 		= $penempatan->scopes;
			$penempatan_baru->policies 		= $penempatan->policies;
			$penempatan_baru->tanggal_masuk = request()->get('tanggal_pindah');
			$penempatan_baru->save();
			DB::commit();

			return redirect(route('karyawan.show', ['id' => $penempatan->orang_id, 'kantor_aktif_id' => $this->kantor_aktif['id']]));
		} catch (Exception $e) {
			DB::rollback();
			return redirect(route('karyawan.show', ['id' => $penempatan->orang_id, 'kantor_aktif_id' => $this->kantor_aktif['id']]))->withErrors($e->getMessage());
		}
	}

	private function resign($penempatan_id)
	{
		try {
			DB::beginTransaction();
			$penempatan 	= PenempatanKaryawan::findorfail($penempatan_id);
			$penempatan->tanggal_keluar 	= request()->get('tanggal_keluar');
			$penempatan->save();

			DB::commit();

			return redirect(route('karyawan.show', ['id' => $penempatan->orang_id, 'kantor_aktif_id' => $this->kantor_aktif['id']]));
		} catch (Exception $e) {
			DB::rollback();
			return redirect(route('karyawan.show', ['id' => $penempatan->orang_id, 'kantor_aktif_id' => $this->kantor_aktif['id']]))->withErrors($e->getMessage());
		}
	}

	private function penempatan_baru($orang_id)
	{
		try {
			DB::beginTransaction();
			$penempatan 			= request()->get('kantor');

			if(in_array('keputusan', $penempatan['scopes']) && $penempatan['role']=='pimpinan')
			{
				$penempatan['policies']	= ['keputusan' => ['max:10000000']];
			}
			else
			{
				$penempatan['policies']	= [];
			}
			$penempatan['kantor_id']	= request()->get('kantor_id');
			$penempatan['orang_id']		= $orang_id;
			$penempatan_simpan 			= new PenempatanKaryawan;
			$penempatan_simpan->fill($penempatan);
			$penempatan_simpan->save();

			DB::commit();

			return redirect(route('karyawan.show', ['id' => $penempatan->orang_id, 'kantor_aktif_id' => $this->kantor_aktif['id']]));
		} catch (Exception $e) {
			DB::rollback();
			return redirect(route('karyawan.show', ['id' => $penempatan->orang_id, 'kantor_aktif_id' => $this->kantor_aktif['id']]))->withErrors($e->getMessage());
		}
	}

	public function batch()
	{
		try {
			if(request()->hasfile('karyawan')){
				$file 		= request()->file('karyawan');

				$fn 		= 'karyawan-'.Str::slug(microtime()).'.'.$file->getClientOriginalExtension(); 

				$date 		= Carbon::now();
				$dp 		= $date->format('Y/m/d');

	      		$file->move(public_path().'/'.$dp, $fn); // uploading file to given path

				DB::beginTransaction();
				
				if (($handle = fopen(public_path().'/'.$dp.'/'.$fn, "r")) !== FALSE) 
				{
					$header 		= null;

					while (($data = fgetcsv($handle, 500, ",")) !== FALSE) 
					{
						if ($header === null) 
						{
							$header = $data;
							continue;
						}
					
						$all_row 	= array_combine($header, $data);

						$scopes 	= explode('|', $all_row['scopes']);

						foreach ($scopes as $k => $v) {
							$scopes[$k]	= str_replace('manage_', '', $v);
						}

						$policies 		= [];

						if($all_row['role']=='pimpinan')
						{
							$policies 	= ['keputusan' => ['max:10000000']];
						}

						$orang 		= Orang::where('nip', $all_row['nip'])->first();
						if(!$orang)
						{
							$orang 	= new Orang;
						}

						$karyawan 	= [
							'nama'			=> $all_row['nama'],
							'email'			=> $all_row['email'],
							'password'		=> $all_row['kode_kantor'],
							'telepon'		=> $all_row['nomor_telepon'],
							'alamat'		=> ['alamat' => $all_row['alamat']],
						];

						$orang->fill($karyawan);
						$orang->save();

						$penempatan_karyawan 	= [
							'kantor_id'		=> $all_row['kode_kantor'],
							'orang_id'		=> $orang['id'],
							'role'			=> $all_row['jabatan'],
							'scopes'		=> $scopes,
							'policies'		=> $policies,
							'tanggal_masuk'	=> $all_row['tanggal_masuk'],
						];
						$penempatan		= new PenempatanKaryawan;
						$penempatan->fill($penempatan_karyawan);
						$penempatan->save();
					}
					fclose($handle);
				}
				DB::commit();
			}
			return redirect(route('karyawan.index', ['kantor_aktif_id' => $this->kantor_aktif['id']]));
		} catch (Exception $e) {
			DB::rollback();
			return redirect()->back()->withInput()->withErrors($e->getMessage());
		}
	}

}