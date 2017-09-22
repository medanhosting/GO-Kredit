<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Putusan;

use Exception, Auth;
use Carbon\Carbon;

use Thunderlabid\Pengajuan\Traits\IDRTrait;

class PutusanController extends Controller
{
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();

		$this->middleware('scope:keputusan');
	}

	public function store($id = null)
	{
		try {
			$putusan 			= Putusan::where('pengajuan_id', $id)->wherehas('pengajuan', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->orderby('tanggal', 'desc')->first();

			if(!$putusan)
			{
				$putusan 		= new Putusan;
			}

			$data_input 						= request()->all();
			$data_input['pembuat_keputusan']	= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
			$data_input['pengajuan_id']			= $id;
			$data_input['provisi']				= $this->formatMoneyTo(($this->formatMoneyFrom($data_input['plafon_pinjaman']) * $data_input['perc_provisi'])/100);

			$putusan->fill($data_input);
			$putusan->save();

			return redirect(route('pengajuan.pengajuan.show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id'), 'status' => $data_input['putusan']]));

		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}

	}

	public function update($id)
	{
		return $this->store($id);
	}
}
