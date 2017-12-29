<?php

namespace App\Http\Controllers\V2\Kantor;

use App\Http\Controllers\Controller;

use Thunderlabid\Manajemen\Models\Kantor;

use Illuminate\Support\Str;
use Exception, DB, Auth, Carbon\Carbon;

class KantorController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$kantor		= new Kantor;

		if(request()->has('q')){
			$kantor = $kantor->where('nama', 'like', '%'.request()->get('q').'%');
		}

		if(request()->has('jenis')){
			switch (strtolower(request()->get('jenis'))) {
				case 'bpr':
				case 'koperasi':
					$kantor 	= $kantor->where('jenis', request()->get('jenis'));
					break;
			}
		}
		
		if(request()->has('tipe')){
			switch (strtolower(request()->get('tipe'))) {
				case 'holding':
				case 'pusat':
				case 'cabang':
					$kantor 	= $kantor->where('tipe', request()->get('tipe'));
					break;
			}
		}

		if(request()->has('sort')){
			switch (strtolower(request()->get('sort'))) {
				case 'nama-asc':
					$kantor 	= $kantor->orderby('nama', 'asc');
					break;
				default:
					$kantor 	= $kantor->orderby('nama', 'desc');
					break;
			}
		}else{
			$kantor = $kantor->orderby('nama', 'asc');
		}

		$kantor 	= $kantor->paginate(15, ['*'], 'kantor');

		view()->share('is_kantor_tab', 'show active');

		view()->share('active_submenu', 'kantor');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kantor.index', compact('kantor'));
		return $this->layout;
	}

	public function create ($id = null) 
	{
		$kantor 				= Kantor::findornew($id);

		view()->share('active_submenu', 'kantor');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kantor.create', compact('kantor'));
		return $this->layout;
	}

	public function store($id = null) 
	{
		try {
			$kantor 				= request()->only('nama', 'tipe', 'jenis', 'kantor_id', 'telepon');
			$kantor['alamat']		= ['alamat' => request()->get('alamat')];
			$kantor['geolocation']	= ['latitude' => request()->get('latitude'), 'longitude' => request()->get('longitude')];

			$kantor_simpan 	= Kantor::findornew($id);
			$kantor_simpan->fill($kantor);
			$kantor_simpan->save();

			return redirect()->route('kantor.index', ['kantor_aktif_id' => $this->kantor_aktif['id']]);
		} catch (Exception $e) {
			return redirect()->back()->withInput()->withErrors($e->getMessage());
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
			$kantor		= Kantor::findorfail($id);
			if (!$kantor)
			{
				throw new Exception("Data tidak ada!", 1);
			}
			
			foreach ($kantor->penempatan as $k => $v) {
				$v->delete();
			}

			$kantor->delete();

			return redirect(route('manajemen.kantor.index', ['kantor_aktif_id' => $this->kantor_aktif['id']]));
		} catch (Exception $e) {
			return redirect(route('manajemen.kantor.index', ['kantor_aktif_id' => $this->kantor_aktif['id']]))->withErrors($e->getMessage());
		}
	}

	public function batch()
	{
		try {
			if(request()->hasfile('kantor')){
				$file 		= request()->file('kantor');

				$fn 		= 'kantor-'.Str::slug(microtime()).'.'.$file->getClientOriginalExtension(); 

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

						$pusat 		= Kantor::findornew($all_row['kode_pusat']);

						$kantor_baru	= Kantor::findornew($all_row['kode_kantor']);

						$kantor 	= [
							'id'			=> strtoupper(str_replace(' ', '', $all_row['nama_kantor'])),
							'kantor_id'		=> $pusat->id,
							'nama'			=> $all_row['nama_kantor'],
							'kode'			=> $all_row['kode_kantor'],
							'jenis'			=> $all_row['jenis_kantor'],
							'tipe'			=> $all_row['tipe_kantor'],
							'geolocation'	=> ['latitude' => $all_row['latitude'], 'longitude' => $all_row['longitude']],
							'telepon'		=> $all_row['nomor_telepon'],
							'alamat'		=> ['alamat' => $all_row['alamat']],
						];

						$kantor_baru->fill($kantor);
						$kantor_baru->save();
					}
					fclose($handle);
				}
				DB::commit();
			}
			return redirect(route('kantor.index', ['kantor_aktif_id' => $this->kantor_aktif['id']]));
		} catch (Exception $e) {
			DB::rollback();
			return redirect()->back()->withInput()->withErrors($e->getMessage());
		}
	}
}