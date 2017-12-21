<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Status;

use Exception, Auth;

class RealisasiController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$realisasi 	= Pengajuan::whereHas('putusan', function($q){$q->where('putusan', 'setuju');})->status('putusan')->where('p_status.progress', 'sedang')->kantor(request()->get('kantor_aktif_id'))->paginate(15, ['*'], 'realisasi');

		view()->share('is_realisasi_tab', 'show active');

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.realisasi.index', compact('realisasi'));
		return $this->layout;
	}

	public function show($id){
		try {
			$realisasi 				= Putusan::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();

			view()->share('active_submenu', 'kredit');
			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

			$this->layout->pages 	= view('v2.realisasi.show', compact('realisasi'));
			return $this->layout;
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function store($id = null)
	{
		try {
			\DB::beginTransaction();
			$putusan					= Putusan::where('pengajuan_id', $id)->wherehas('pengajuan', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->orderby('tanggal', 'desc')->first();

			if(request()->has('checklists')){
				$data_input['checklists'] 	= request()->get('checklists');
				$putusan->fill($data_input);
				$putusan->save();
			}

			if(request()->has('tanggal_realisasi')){
				$status 				= new Status;
				$status->tanggal 		= request()->get('tanggal_realisasi');
				$status->progress 		= 'sedang';
				$status->status 		= 'realisasi';
				$status->karyawan 		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
				$status->pengajuan_id 	= $id;
				$status->save();
			}

			if(request()->has('tanggal_pencairan')){
				$status 				= new Status;
				$status->tanggal 		= request()->get('tanggal_pencairan');
				$status->progress 		= 'sudah';
				$status->status 		= 'realisasi';
				$status->karyawan 		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
				$status->pengajuan_id 	= $id;
				$status->save();
			}

			\DB::commit();
			return redirect(route('realisasi.show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]));

		} catch (Exception $e) {
			\DB::rollback();
			return redirect()->back()->withErrors($e->getMessage());
		}

	}

	public function update($id)
	{
		return $this->store($id);
	}
}
