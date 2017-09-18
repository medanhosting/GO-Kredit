<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\Jaminan;

use Exception;
use Session;
use MessageBag;

class PermohonanController extends Controller
{
	protected $view_dir = 'pengajuan.permohonan.';

	public function index () 
	{
		$permohonan 			= Pengajuan::status('permohonan')->kantor(request()->get('kantor_aktif_id'))->with(['status_terakhir', 'jaminan'])->orderby('created_at', 'desc')->paginate();

		view()->share('permohonan', $permohonan);
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
		$this->layout->pages 	= view($this->view_dir . 'index');

		return $this->layout;
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
			$permohonan 			= Pengajuan::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->with('jaminan')->first();
			if (!$permohonan)
			{
				$permohonan 		= new Pengajuan;
			}

			$data_input 				= request()->all();
			$data_input['kode_kantor']	= request()->get('kantor_aktif_id');
			$data_input['is_mobile'] 	= false;
			$data_input['dokumen_pelengkap']['ktp'] = 'http://koperasipro.dev/2017/09/05/ktp-000847600-1504576351.jpeg';
			$data_input['dokumen_pelengkap']['kk'] = 'http://koperasipro.dev/2017/09/05/ktp-000847600-1504576351.jpeg';

			if (isset($data_input['nasabah']['nik'])) {
				$data_input['nasabah']['nik'] = '35-' . $data_input['nasabah']['nik'];
			}

			$permohonan->fill($data_input);
			$permohonan->save();

			foreach ($permohonan->jaminan as $key => $value)
			{
				$value->delete();
			}

			if (request()->has('jaminan'))
			{
				foreach (request()->get('jaminan') as $key => $value) 
				{
					$value['pengajuan_id']	= $permohonan->id;
					$jaminan 				= new Jaminan;
					$jaminan->fill($value);
					$jaminan->save();
				}
			}
	
			return redirect(route($this->view_dir . 'show', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]));
		} catch (Exception $e) {
			if (!is_null($id))
			{
				return redirect(route($this->view_dir . 'edit', ['id' => $id, 'kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
			}

			$errors = new MessageBag();
			foreach ($e->getMessage()->toArray() as $k => $error)
			{
				foreach ($error as $x)
				{
					$errors->add(str_replace('.', '_', $k), $x);
				}
			}
			// return redirect()->back()->withErrors($errors)->withInput();

			return redirect(route($this->view_dir . 'create', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($errors)->withInput();
	}

	public function show($id)
	{
		try {
			$permohonan		= Pengajuan::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->with('jaminan', 'riwayat_status', 'status_terakhir')->first();

			if (!$permohonan)
			{
				throw new Exception("Data tidak ada!", 1);
			}
			
			view()->share('permohonan', $permohonan);
			view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

			$this->layout->pages 	= view('pengajuan.show');
			return $this->layout;

		} catch (Exception $e) {
			return redirect(route('pengajuan.pengajuan.index', ['kantor_aktif_id' => request()->get('kantor_aktif_id')]))->withErrors($e->getMessage());
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
			'tidak_bekerja'		=> 'Belum / Tidak Bekerja',
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
			'shm'			=> 'Surat Hak Milik',
			'shgb'			=> 'Surat Hak Guna Bangunan'
		];

		$status_perkawinan = [
			'belum_kawin'	=> 'Belum Kawin',
			'kawin'			=> 'Kawin',
			'cerai'			=> 'Cerai',
			'cerai_mati'	=> 'Cerai Mati'
		];

		$list_kota = [
			'kota malang' => 'Kota Malang',
			'kabupaten malang' => 'Kabupaten Malang'
		];

		$list_kecamatan = [
			'blimbing' => 'Blimbing',
			'klojen' => 'Klojen',
			'lowokwaru' => 'Lowokwaru',
			'kedungkandang' => 'Kedungkandang'
		];

		$list_kelurahan = [
			'rampal celaket' => 'Rampal Celaket',
			'Buring' => 'Buring',
			'lowokwaru' => 'Lowokwaru',
			'blimbing' => 'Blimbing'
		];

		view()->share('jenis_pekerjaan', $jenis_pekerjaan);
		view()->share('jenis_kendaraan', $jenis_kendaraan);
		view()->share('jenis_sertifikat', $jenis_sertifikat);
		view()->share('status_perkawinan', $status_perkawinan);
		view()->share('list_kota', $list_kota);
		view()->share('list_kecamatan', $list_kecamatan);
		view()->share('list_kelurahan', $list_kelurahan);
	}
}
