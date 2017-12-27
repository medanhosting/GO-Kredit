<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\SuratPeringatan;
use Thunderlabid\Kredit\Models\MutasiJaminan;

use App\Http\Service\Policy\FeedBackPenagihan;

use Exception, DB, Auth, Carbon\Carbon;

class KreditController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index () 
	{
		$aktif 		= Aktif::kantor(request()->get('kantor_aktif_id'))->PembayaranBerikut()->with(['jaminan'])->paginate(15, ['*'], 'aktif');

		view()->share('is_aktif_tab', 'show active');

		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.index', compact('aktif'));
		return $this->layout;
	}

	public function show($id) 
	{
		$aktif 		= Aktif::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->PembayaranBerikut()->first();

		//ANGSURAN
		$angsuran 	= AngsuranDetail::displaying()->where('nomor_kredit', $aktif['nomor_kredit'])->get();
		$total		= array_sum(array_column($angsuran->toArray(), 'subtotal'));

		//TUNGGAKAN
		$today		= Carbon::now();
		$tunggakan  = AngsuranDetail::where('nomor_kredit', $aktif['nomor_kredit'])->wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($today)->orderby('tanggal', 'asc')->first();

		$latest_pay = NotaBayar::where('nomor_kredit', $aktif['nomor_kredit'])->orderby('tanggal', 'desc')->first();
		$riwayat_t 	= SuratPeringatan::where('nth', '<>', $tunggakan['nth'])->orderby('nth', 'desc')->get();

		$jaminan 	= MutasiJaminan::where('nomor_kredit', $aktif['nomor_kredit'])->get();

		if(request()->get('current')){
			switch (strtolower(request()->get('current'))) {
				case 'tunggakan':
					view()->share('is_tunggakan_tab', 'show active');
					break;
				default:
					view()->share('is_kredit_tab', 'show active');
					break;
			}
		}else{
			view()->share('is_kredit_tab', 'show active');
		}


		view()->share('active_submenu', 'kredit');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.show', compact('aktif', 'angsuran', 'total', 'tunggakan', 'latest_pay', 'riwayat_t', 'jaminan'));
		return $this->layout;
	}

	public function update($id) 
	{
		try {
			$aktif 		= Aktif::where('id', $id)->kantor(request()->get('kantor_aktif_id'))->first();
			
			DB::BeginTransaction();
			switch (request()->get('current')) {
				case 'tagihan':
					$feedback 	= new FeedBackPenagihan($aktif, request()->get('nip_karyawan'), request()->get('tanggal'), request()->get('penerima'), request()->get('nominal'));
					$feedback->bayar();
					break;
				default:
					$nth 		= request()->get('nth');
					$angsuran 	= AngsuranDetail::whereIn('nth', $nth)->get();

					if($angsuran){
						$nb 	= new NotaBayar;
						$nb->nomor_faktur 	= NotaBayar::generatenomorfaktur($angsuran[0]['nomor_kredit']);
						$nb->nomor_kredit 	= $angsuran[0]['nomor_kredit'];
						$nb->tanggal 		= Carbon::now()->format('d/m/Y H:i');
						$nb->nip_karyawan 	= Auth::user()['nip'];
						$nb->save();

						foreach ($angsuran as $k => $v) {
							if(is_null($v->nota_bayar_id)){
								$v->nota_bayar_id 	= $nb->id;
								$v->save();
							}
						}
					}
					break;
			}
			
			DB::commit();
			return redirect()->back();
		} catch (Exception $e) {
			DB::rollback();
			dD($e);
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function store(){
		return $this->update(request()->get('id'));
	}
}
