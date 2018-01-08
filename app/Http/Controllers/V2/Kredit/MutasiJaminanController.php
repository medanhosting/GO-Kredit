<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\MutasiJaminan;

use Exception, Carbon\Carbon, DB;

class MutasiJaminanController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$jaminan 	= MutasiJaminan::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));});

		$start 		= Carbon::now()->startofday();
		$end 		= Carbon::now()->endofday();

		if(request()->has('start')){
			$start		= Carbon::createFromFormat('d/m/Y', request()->get('start'))->startofday();
			$jaminan 	= $jaminan->where('tanggal', '>=', $start->format('Y-m-d H:i:s'))->where('tanggal', '<=', $end->format('Y-m-d H:i:s'))->orderby('tanggal', 'desc');
		}
		if(request()->has('end')){
			$end		= Carbon::createFromFormat('d/m/Y', request()->get('end'))->endofday();
			$jaminan 	= $jaminan->where('tanggal', '>=', $start->format('Y-m-d H:i:s'))->where('tanggal', '<=', $end->format('Y-m-d H:i:s'))->orderby('tanggal', 'desc');
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
			$jaminan	= $jaminan->whereRaw(DB::raw('documents REGEXP "'.$regexp2.'"'));
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

			//simpan jaminan keluar
			if(str_is($jaminan->possible_action, 'ajukan_jaminan_keluar')){
				$out 	= new MutasiJaminan;
				$out->nomor_kredit 	= $jaminan->nomor_kredit;
				$out->tanggal 		= request()->get('out'). ' 00:00';
				$out->tag 			= 'out';
				$out->description 	= request()->get('description');
				$out->documents 	= $jaminan->documents;
				$out->nomor_jaminan = $jaminan->nomor_jaminan;
				$out->status 		= 'pending';
				$out->save();

				$out 	= new MutasiJaminan;
				$out->nomor_kredit 	= $jaminan->nomor_kredit;
				$out->tanggal 		= request()->get('in'). ' 00:00';
				$out->tag 			= 'in';
				$out->description 	= request()->get('description');
				$out->documents 	= $jaminan->documents;
				$out->nomor_jaminan = $jaminan->nomor_jaminan;
				$out->status 		= 'pending';
				$out->save();
			}elseif(str_is($jaminan->possible_action, 'otorisasi_jaminan_masuk')){
				$jaminan->status 	= 'completed';
				$jaminan->save();
			}elseif(str_is($jaminan->possible_action, 'otorisasi_jaminan_keluar')){
				$jaminan->status 	= 'completed';
				$jaminan->save();
			}

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
