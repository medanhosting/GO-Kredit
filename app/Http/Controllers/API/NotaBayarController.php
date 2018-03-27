<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller as BaseController;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\SuratPeringatan;

use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\CetakNotaBayar;
use Thunderlabid\Manajemen\Models\Kantor;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use App\Exceptions\AppException;
use App\Service\Api\ResponseTrait;
use App\Http\Service\Policy\FeedBackPenagihan;

use Exception, Response, Auth, Carbon\Carbon, Config, DB, Validator;

class NotaBayarController extends BaseController
{
	use ResponseTrait;

	public function __construct(){

		if(!Auth::check()){
			$this->middleware('auth:api');
		}
	}

	public function index()
	{
		try {
			//1. find pengajuan
			if(Auth::user()){
				$k		= PenempatanKaryawan::where('orang_id', Auth::user()['id'])->active(Carbon::now());
				$nb		= new NotaBayar;

				if(request()->has('kode_kantor')){
					$k	= $k->where('kantor_id', request()->get('kode_kantor'));
				}

				$k 		= $k->first();
				
				// if(request()->has('query')){
				// 	$regexp 	= preg_replace("/-+/",'[^A-Za-z0-9_]+',request()->get('query'));
				// 	$nb 	= $nb->where(function($q)use($regexp){$q->whereHas('collateral', function($q)use($regexp){$q->whereRaw(\DB::raw("dokumen_survei REGEXP '". $regexp."'"));})->orwherehas('pengajuan', function($q)use($regexp){$q->whereraw(\DB::raw("nasabah REGEXP '". $regexp."'"));});});
				// }

				$nb 	= $nb->Where('karyawan->nip', $k['orang']['nip'])->where('jenis', 'kolektor')->orderby('tanggal', 'desc')->paginate();

				$nb->appends(request()->only('kode_kantor', 'query'));
			
				return response()->json(['status' => 1, 'data' => $nb, 'error' => ['message' => []]]);
			}

			return response()->json(['status' => 1, 'data' => [], 'error' => ['message' => []]]);

		} catch (Exception $e) {
			return $this->error_response(request()->all(), $e);
		}
	}

	public function show($id)
	{
		try {
			//1. find pengajuan
			if(Auth::user()){
				$k		= PenempatanKaryawan::where('orang_id', Auth::user()['id'])->active(Carbon::now());
				$nb		= new NotaBayar;

				
				// if(request()->has('query')){
				// 	$regexp 	= preg_replace("/-+/",'[^A-Za-z0-9_]+',request()->get('query'));
				// 	$nb 	= $nb->where(function($q)use($regexp){$q->whereHas('collateral', function($q)use($regexp){$q->whereRaw(\DB::raw("dokumen_survei REGEXP '". $regexp."'"));})->orwherehas('pengajuan', function($q)use($regexp){$q->whereraw(\DB::raw("nasabah REGEXP '". $regexp."'"));});});
				// }

				$nb 	= $nb->where('karyawan->nip', Auth::user()['nip'])->where('jenis', 'kolektor')->where('id', $id)->with(['details'])->first()->toarray();

				if(request()->has('kode_kantor')){
					$k	= $k->where('kantor_id', request()->get('kode_kantor'));
				}else{
					$nf = explode('.', $nb['nomor_faktur']);
					$k	= $k->where('kantor_id', $nf[0].'.'.$nf[1]);
				}

				$k 		= $k->first();

				$tagihan = Penagihan::where('nomor_faktur', $nb['nomor_faktur'])->first();
				$nb['penerima']	= $tagihan['penerima'];
				$nb['kantor']	= $k['kantor'];

				return response()->json(['status' => 1, 'data' => $nb, 'error' => ['message' => []]]);
			}

			return response()->json(['status' => 1, 'data' => [], 'error' => ['message' => []]]);

		} catch (Exception $e) {
			return $this->error_response(request()->all(), $e);
		}
	}

	// INPUT :
	// Nomor Kredit
	// Nomor SP
	// Nama Penerima
	// Nominal
	public function store()
	{
		try {
			DB::beginTransaction();

			//1. find pengajuan
			if(Auth::user()){
				$data 	= request()->only('nomor_kredit', 'penerima', 'jumlah', 'nomor_sp');

				$rules['nomor_kredit']	= ['required'];
				$rules['penerima.nama']	= ['required'];
				$rules['jumlah']		= ['required'];
	
				//////////////
				// Validate //
				//////////////
				$validator = Validator::make($data, $rules);
				if ($validator->fails())
				{
					throw new AppException($validator->messages(), 1);
				}

				$k		= PenempatanKaryawan::where('orang_id', Auth::user()['id'])->active(Carbon::now());
				$kre 	= Aktif::where('nomor_kredit', request()->get('nomor_kredit'))->first();

				if(request()->has('kode_kantor')){
					$k	= $k->where('kantor_id', request()->get('kode_kantor'));
				}else{
					$k	= $k->where('kantor_id', $kre->kode_kantor);
				}
				
				$k 		= $k->first();

				if(request()->has('nomor_sp')){
					$sp = SuratPeringatan::nomorsurat(request()->get('nomor_sp'))->first();
				}else{
					$sp = null;
				}

				request()->merge(['kantor_aktif_id' => $k->kantor_id]);

				$kary 	= ['nip' => $k['orang']['nip'], 'nama' => $k['orang']['nama']];
				$today 	= Carbon::now();
				
				$feedback 	= new FeedBackPenagihan($kre, $kary, $today->format('d/m/Y H:i'), request()->get('penerima'), request()->get('jumlah'), Config::get('finance.nomor_perkiraan_titipan_kolektor'), $sp['id']);
				$feedback 	= $feedback->bayar();

				$return['sp']			= $sp;
				$return['penagihan']	= $feedback;
				if($feedback->nomor_faktur){
					$return['notabayar']= NotaBayar::where('nomor_faktur', $feedback->nomor_faktur)->with(['details'])->first()->toarray();

					$tagihan 			= Penagihan::where('nomor_faktur', $feedback->nomor_faktur)->first();
					$return['penerima']	= $tagihan['penerima'];
					$return['kantor']	= $k['kantor'];
				}

				DB::commit();
				
				return response()->json(['status' => 1, 'data' => $return, 'error' => ['message' => []]]);
			}
			DB::rollback();

			return response()->json(['status' => 1, 'data' => [], 'error' => ['message' => []]]);

		} catch (Exception $e) {
			DB::rollback();
			return $this->error_response(request()->all(), $e);
		}
	}


	public function qr($code)
	{
		try {
			$hashed		= NotaBayar::simplecrypt($code, 'd');

			$nb	= NotaBayar::where('nomor_faktur', $hashed)->with(['details'])->first()->toarray();

			$nf = explode('.', $nb['nomor_faktur']);
			$k	= Kantor::where('id', $nf[0].'.'.$nf[1])->first();

			$tagihan = Penagihan::where('nomor_faktur', $nb['nomor_faktur'])->first();
			$nb['penerima']	= $tagihan['penerima'];
			$nb['kantor']	= $k['kantor'];

			return response()->json(['status' => 1, 'data' => $nb, 'error' => ['message' => []]]);

		} catch (Exception $e) {
			return $this->error_response(request()->all(), $e);
		}
	}

	public function print($faktur){
		try {
			$cetak 		= new CetakNotaBayar;
			$cetak->nomor_faktur 	= $faktur;
			$cetak->tanggal 		= Carbon::now()->format('d/m/Y H:i');
			$cetak->karyawan 		= ['nip' => Auth::user()->nip, 'nama' => Auth::user()->nama];
			$cetak->save();

			return response()->json(['status' => 1, 'data' => $cetak, 'error' => ['message' => []]]);

		} catch (Exception $e) {
			return $this->error_response(request()->all(), $e);
		}
	}

}
