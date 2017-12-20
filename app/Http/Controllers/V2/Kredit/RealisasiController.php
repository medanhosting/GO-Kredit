<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Pengajuan\Models\Pengajuan;

use Exception;

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
			$putusan 					= Putusan::where('pengajuan_id', $id)->wherehas('pengajuan', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->orderby('tanggal', 'desc')->first();
			
			$data_input['checklists'] 	= request()->get('checklists');

			$putusan->fill($data_input);
			$putusan->save();

			return redirect(route('realisasi.show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]));

		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}

	}

	public function update($id)
	{
		return $this->store($id);
	}
}
