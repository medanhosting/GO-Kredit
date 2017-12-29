<?php

namespace App\Http\Controllers\V2\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Status;

use App\Http\Controllers\V2\Traits\PengajuanTrait;
use Exception, Auth, DB;

class PutusanController extends Controller
{
	use PengajuanTrait;
	
	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$setuju 	= $this->get_pengajuan('setuju');
		$tolak 		= $this->get_pengajuan('tolak');

		if(request()->has('current')){
			switch (request()->get('current')) {
				case 'tolak':
					view()->share('is_tolak_tab', 'active show');
					break;
				case 'setuju':
					view()->share('is_setuju_tab', 'active show');
					break;
			}			
		}else{
			view()->share('is_setuju_tab', 'active show');
		}

		view()->share('active_submenu', 'putusan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.putusan.index', compact('setuju', 'tolak'));
		return $this->layout;
	}

	public function show($id){
		try {
			$realisasi 				= Putusan::where('pengajuan_id', $id)->orderby('tanggal', 'desc')->first();

			view()->share('active_submenu', 'putusan');
			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

			$this->layout->pages 	= view('v2.putusan.show', compact('realisasi'));
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
