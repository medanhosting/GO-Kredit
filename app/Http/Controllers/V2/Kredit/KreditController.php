<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\SuratPeringatan;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\MutasiJaminan;

use App\Http\Service\Policy\FeedBackPenagihan;
use App\Http\Service\Policy\PelunasanAngsuran;

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

		$titipan 	= NotaBayar::wheredoesnthave('details',  function($q){$q;})->where('nomor_kredit', $aktif['nomor_kredit'])->sum('jumlah');

		$total		= array_sum(array_column($angsuran->toArray(), 'subtotal'));
		
		//TUNGGAKAN
		$today		= Carbon::now();
		$dday		= Carbon::createFromFormat('d/m/Y H:i', $aktif['tanggal']);
		$tunggakan  = AngsuranDetail::where('nomor_kredit', $aktif['nomor_kredit'])->wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($today)->orderby('tanggal', 'asc')->first();
		
		$riwayat_t  = AngsuranDetail::where('nomor_kredit', $aktif['nomor_kredit'])->wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($dday)->orderby('tanggal', 'asc')->get();

		$latest_pay = NotaBayar::where('nomor_kredit', $aktif['nomor_kredit'])->orderby('tanggal', 'desc')->first();

		$penagihan 	= Penagihan::wherehas('kredit', function($q)use($id){$q->where('kode_kantor', request()->get('kantor_aktif_id'))->where('id', $id);})->HitungNotaBayar()->orderby('tanggal', 'asc')->get();

		$jaminan 	= MutasiJaminan::where('nomor_kredit', $aktif['nomor_kredit'])->with(['next'])->get();

		if(request()->get('current')){
			switch (strtolower(request()->get('current'))) {
				case 'tunggakan':
					view()->share('is_tunggakan_tab', 'show active');
					break;
				case 'penagihan':
					view()->share('is_penagihan_tab', 'show active');
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

		view()->share('aktif', $aktif);
		view()->share('angsuran', $angsuran);
		view()->share('total', $total);
		view()->share('tunggakan', $tunggakan);
		view()->share('latest_pay', $latest_pay);
		view()->share('riwayat_t', $riwayat_t);
		view()->share('penagihan', $penagihan);
		view()->share('jaminan', $jaminan);

		view()->share('kredit_id', $id);

		$this->layout->pages 	= view('v2.kredit.show');
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
					
					$angsuran 	= AngsuranDetail::whereIn('nth', $nth)->where('nomor_kredit', $aktif['nomor_kredit'])->wherenull('nota_bayar_id')->get();

					$latest_pay = AngsuranDetail::where('nomor_kredit', $aktif['nomor_kredit'])->wherenotnull('nota_bayar_id')->wherein('tag', ['bunga', 'pokok'])->orderby('nth', 'desc')->first();
					$should_pay = AngsuranDetail::displaying()->where('nomor_kredit', $aktif['nomor_kredit'])->whereIn('nth', $nth)->get();

					if($latest_pay){
						$total 	= $aktif['jangka_waktu'] - $latest_pay['nth'];
					}else{
						$total 	= $aktif['jangka_waktu'];
					}

					$potongan 		= false;

					if(count($should_pay) == $total){
						$potongan 	= PelunasanAngsuran::potongan($aktif['nomor_kredit']);
					}

					if($angsuran){
						$nb 	= new NotaBayar;
						$nb->nomor_faktur 	= NotaBayar::generatenomorfaktur($aktif['nomor_kredit']);
						$nb->nomor_kredit 	= $aktif['nomor_kredit'];
						$nb->tanggal 		= Carbon::now()->format('d/m/Y H:i');
						$nb->nip_karyawan 	= Auth::user()['nip'];
						$nb->save();

						foreach ($angsuran as $k => $v) {
							if(is_null($v->nota_bayar_id)){
								$v->nota_bayar_id 	= $nb->id;
								$v->save();
							}
						}

						if($potongan){
							$pad 	= new AngsuranDetail;
							$pad->nota_bayar_id	= $nb->id;
							$pad->nomor_kredit 	= $aktif['nomor_kredit'];
							$pad->tanggal 		= Carbon::now()->format('d/m/Y H:i');
							$pad->nth 			= ($latest_pay['nth'] * 1) + 1;
							$pad->tag 			= 'potongan';
							$pad->amount 		= $potongan;
							$pad->description 	= 'Potongan Pelunasan';
							$pad->save();
						}
					}
					break;
			}
			DB::commit();
			return redirect()->back();
		} catch (Exception $e) {
			DB::rollback();
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function store(){
		return $this->update(request()->get('id'));
	}
}
