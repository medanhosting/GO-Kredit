<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\MutasiJaminan;

use Exception, Carbon\Carbon, DB, Auth;

class MutasiJaminanController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->middleware('scope:operasional.jaminan')->only(['index']);

		$this->middleware('scope:mutasi_jaminan')->only(['update', 'store']);
		$this->middleware('required_password')->only(['update', 'store']);
	}

	public function index () 
	{
		$jaminan 	= MutasiJaminan::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));});

		$start 		= Carbon::now()->startofday();
		$end 		= Carbon::now()->endofday();

		if(request()->has('start')){
			$start		= Carbon::createFromFormat('d/m/Y', request()->get('start'))->startofday();
			$jaminan 	= $jaminan->where('tanggal', '>=', $start->format('Y-m-d H:i:s'));
		}
		if(request()->has('end')){
			$end		= Carbon::createFromFormat('d/m/Y', request()->get('end'))->endofday();
			$jaminan 	= $jaminan->where('tanggal', '<=', $end->format('Y-m-d H:i:s'));
		}

		if (request()->has('q_jaminan'))
		{
			$cari		= request()->get('q_jaminan');
			$regexp 	= preg_replace("/-+/",'[^A-Za-z0-9_]+',$cari);
			$jaminan	= $jaminan->wherehas('kredit', function($q)use($regexp){
				$q->whereRaw(DB::raw('nasabah REGEXP "'.$regexp.'"'));
			});
		}

		if (request()->has('doc_jaminan'))
		{
			$cari2		= request()->get('doc_jaminan');
			$regexp2 	= preg_replace("/-+/",'[^A-Za-z0-9_]+',$cari2);
			$jaminan	= $jaminan->whereRaw(DB::raw('dokumen REGEXP "'.$regexp2.'"'));
		}

		if (request()->has('mutasi_jaminan'))
		{
			$jaminan	= $jaminan->where('tag', request()->get('mutasi_jaminan'));
		}

		if (request()->has('sort_jaminan'))
		{
			switch (strtolower(request()->get('sort_jaminan'))) {
				case 'tanggal-desc':
					$jaminan 	= $jaminan->orderby('tanggal', 'desc');
					break;
				default:
					$jaminan 	= $jaminan->orderby('tanggal', 'asc');
					break;
			}
		}else{
			$jaminan 	= $jaminan->orderby('tanggal', 'asc');
		}

		$jaminan 	= $jaminan->paginate();

		view()->share('active_submenu', 'jaminan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.jaminan.index', compact('jaminan'));
		return $this->layout;
	}

	public function store($id = null){
		try {
			DB::BeginTransaction();

			//find jaminan
			$jaminan  	= MutasiJaminan::findorfail($id);
			$data 		= request()->only('tanggal','stok','deskripsi');

			$tag 		= 'in';
			switch (strtolower($data['stok'])) {
				case 'titipan':
				case 'hapus_buku':
				case 'bermasalah':
				case 'keluar_bukan_pelunasan':
					$tag = 'out';
					break;
			}

			//simpan jaminan 
			$jj 		= new MutasiJaminan;
			$jj->mutasi_jaminan_id	= $jaminan->id;
			$jj->nomor_kredit 		= $jaminan->nomor_kredit;
			$jj->kategori 			= $jaminan->kategori;
			$jj->tanggal 			= $data['tanggal']. ' 00:00';
			$jj->tag 				= $tag;
			$jj->status 			= $data['stok'];
			$jj->deskripsi 			= $data['deskripsi'];
			$jj->dokumen 			= $jaminan->dokumen;
			$jj->nomor_jaminan 		= $jaminan->nomor_jaminan;
			$jj->karyawan 			= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
			$jj->save();

			DB::commit();
			return redirect()->back();
		} catch (Exception $e) {
			DB::rollback();
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function update($id){
		return $this->store($id);
	}
}
