<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;

use Exception;
use Session;
class PermohonanController extends Controller
{
	public function index() 
	{
		$permohonan 			= Pengajuan::status('permohonan')->kantor(request()->get('kantor_aktif_id'))->with(['status_terakhir'])->paginate();

		$this->layout->pages 	= view('dashboard.overview', compact('permohonan'));

		return $this->layout;
	}

	public function create($id = null)
	{
		try {
			$permohonan 			= Pengajuan::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->with('jaminan')->first();
			if(!$permohonan && is_null($id))
			{
				$permohonan 		= new Pengajuan;
			}
			elseif(!$permohonan)
			{
				throw new Exception("Data tidak ada, Silahkan buat permohonan baru.", 1);
			}

			$this->layout->pages 	= view('dashboard.overview', compact('permohonan'));
			return $this->layout;
		} catch (Exception $e) {
			return redirect(route('permohonan.create', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
		}
	}

	public function store($id = null)
	{
		try {
			$permohonan 			= Pengajuan::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->with('jaminan')->first();
			if(!$permohonan)
			{
				$permohonan 		= new Pengajuan;
			}

			$data_input 				= request()->all();
			$data_input['kode_kantor']	= request()->get('kantor_aktif_id');

			$permohonan->fill($data_input);
			$permohonan->save();

			foreach($permohonan->jaminan as $key => $value)
			{
				$value->delete();
			}

			if(request()->has('jaminan'))
			{
				foreach (request()->get('jaminan') as $key => $value) 
				{
					$value['pengajuan_id']	= $permohonan->id;
					$jaminan 				= new Jaminan;
					$jaminan->fill($value);
					$jaminan->save();
				}
			}
	
			return redirect(route('permohonan.show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]));
		} catch (Exception $e) {
			if(!is_null($id))
			{
				return redirect(route('permohonan.edit', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
			}

			return redirect(route('permohonan.create', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
		}

	}

	public function show($id)
	{
		try {
			$permohonan		= Pengajuan::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->with('jaminan', 'riwayat_status', 'status_terakhir')->first();
			
			if(!$permohonan)
			{
				throw new Exception("Data tidak ada!", 1);
			}
		
			$this->layout->pages 	= view('dashboard.overview', compact('permohonan'));
			return $this->layout;

		} catch (Exception $e) {
			return redirect(route('permohonan.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
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
			if(!$permohonan)
			{
				throw new Exception("Data tidak ada!", 1);
			}

			foreach($permohonan->jaminan as $key => $value)
			{
				$value->delete();
			}

			$permohonan->delete();

			return redirect(route('permohonan.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]));
		} catch (Exception $e) {
			return redirect(route('permohonan.show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
		}
	}
}
