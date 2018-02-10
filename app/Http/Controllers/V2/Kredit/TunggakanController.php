<?php

namespace App\Http\Controllers\V2\Kredit;

use App\Http\Controllers\Controller;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Kredit\Models\PermintaanRestitusi;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;
use Thunderlabid\Kredit\Models\SuratPeringatan;

use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use App\Service\Traits\IDRTrait;
use App\Http\Service\Policy\PerhitunganBunga;

use Exception, Auth, Carbon\Carbon;

class TunggakanController extends Controller
{
	use IDRTrait;

	public function __construct()
	{
		parent::__construct();
		$this->middleware('scope:operasional.kredit.angsuran.surat_peringatan')->only(['index']);
		
		$this->middleware('scope:surat_peringatan')->only(['store', 'update']);
		$this->middleware('required_password')->only(['store', 'update']);

		$this->middleware('limit_date:'.implode('|', $this->scopes['scopes']))->only(['update', 'store']);
	}

	public function index() 
	{
		$today 		= Carbon::now();
		view()->share('read_only', false);

		if(request()->has('q')){
			$today	= Carbon::createFromFormat('d/m/Y', request()->get('q'));
			view()->share('read_only', true);
		}

		$tunggakan 	= JadwalAngsuran::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($today)->with(['kredit', 'kredit.penagihan', 'kredit.suratperingatan'])->orderby('tanggal', 'asc')->get();

		view()->share('is_aktif_tab', 'show active');

		view()->share('active_submenu', 'tunggakan');
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$this->layout->pages 	= view('v2.kredit.tunggakan.index', compact('tunggakan'));
		return $this->layout;
	}

	public function store($id = null) 
	{
		$today	= Carbon::createfromformat('d/m/Y H:i', request()->get('tanggal'));

		$tunggakan 	= JadwalAngsuran::where('nomor_kredit', $id)->wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($today)->orderby('tanggal', 'asc')->first();

		try {
			if(!$tunggakan){
				throw new Exception("Tidak ada tunggakan", 1);
			}

			$new_sp 				= new SuratPeringatan; 
			$new_sp->nomor_kredit 	= $tunggakan['nomor_kredit'];
			$new_sp->nth 			= $tunggakan['nth'];
			$new_sp->tanggal 		= request()->get('tanggal');
			$new_sp->tag 			= $tunggakan['kredit']['should_issue_surat_peringatan']['keluarkan'];
			$new_sp->karyawan 		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
			$new_sp->save();
			return redirect()->back();
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function update($id){
		return $this->store($id);
	}

	public function print($id)
	{
		try {
			$surat 			= SuratPeringatan::where('nomor_kredit', $id)->where('id', request()->get('sp_id'))->first();
			$tanggal_surat 	= Carbon::createFromFormat('d/m/Y H:i', $surat->tanggal);

			$t_tunggakan 	= JadwalAngsuran::HitungTunggakanBeberapaWaktuLalu($tanggal_surat)->where('nomor_kredit', $id)->selectraw('count(nth) as jumlah_tunggakan')->first();

			//hitung denda before
			$rd			= JadwalAngsuran::TunggakanBeberapaWaktuLalu($tanggal_surat)->where('nomor_kredit', $id)->groupby('nth')->selectraw('sum(jumlah * ('.$surat->kredit['persentasi_denda'].'/100)) as tunggakan')->selectraw('DATEDIFF(min(tanggal),IFNULL(min(tanggal_bayar),"'.$tanggal_surat->format('Y-m-d H:i:s').'")) as days')->selectraw('nth')->get();

			$before['denda'] 		= 0;
			foreach ($rd as $k => $v) {
				$before['denda'] 	= $before['denda'] + ($v['tunggakan'] * abs($v['days'])); 
			}
			$before['denda']		= $before['denda'] - NotaBayar::where('morph_reference_id', $id)->where('morph_reference_tag', 'kredit')->where('jenis', 'denda')->where('tanggal', '<=', $tanggal_surat->format('Y-m-d H:i:s'))->sum('jumlah') - PermintaanRestitusi::where('nomor_kredit', $id)->where('tanggal', '<=', $tanggal_surat->format('Y-m-d H:i:s'))->where('is_approved', true)->sum('jumlah');

			//hitung titipan middle
			$middle['titipan']	= DetailTransaksi::whereIn('tag', ['titipan_pokok', 'titipan_bunga', 'restitusi_titipan_pokok', 'restitusi_titipan_bunga'])->wherehas('notabayar', function($q)use($id, $tanggal_surat){$q->where('morph_reference_id', $id)->where('morph_reference_tag', 'kredit')->where('tanggal', '<=', $tanggal_surat->format('Y-m-d H:i:s'));})->sum('jumlah');

			//hitung bunga n pokok after
			$hbp	= JadwalAngsuran::where('tanggal', '>', $tanggal_surat->format('Y-m-d H:i:s'))
				->where('nomor_kredit', $id)
				->get(['nth'])
				->toarray()
				;

			if(str_is($surat->kredit['jenis_pinjaman'], 'pa'))
			{
				$rincian 	= new PerhitunganBunga($surat->kredit['plafon_pinjaman'], 'Rp 0', $surat->kredit['suku_bunga'], null, null, null, $surat->kredit['jangka_waktu']);
				$rincian 	= $rincian->pa();
			}
			elseif(str_is($surat->kredit['jenis_pinjaman'], 'pt'))
			{
				$rincian 	= new PerhitunganBunga($surat->kredit['plafon_pinjaman'], 'Rp 0', $surat->kredit['suku_bunga'], null, null, null, $surat->kredit['jangka_waktu']);
				$rincian 	= $rincian->pt();
			}

			$after['pokok']	= 0;
			$after['bunga']	= 0;

			foreach ($hbp as $k => $v) {
				$after['pokok'] 	= $after['pokok'] + $this->formatMoneyFrom($rincian['angsuran'][$v['nth']]['angsuran_pokok']);
				$after['bunga'] 	= $after['bunga'] + $this->formatMoneyFrom($rincian['angsuran'][$v['nth']]['angsuran_bunga']);
			}

			$pimpinan 		= PenempatanKaryawan::where('kantor_id', request()->get('kantor_aktif_id'))->where('role', 'pimpinan')->where('tanggal_masuk', '>=', $tanggal_surat->format('Y-m-d H:i:s'))->where(function($q)use($tanggal_surat){$q->where('tanggal_keluar', '<=', $tanggal_surat->format('Y-m-d H:i:s'))->orwherenull('tanggal_keluar');})->first();

			view()->share('surat', $surat);
			view()->share('tanggal_surat', $tanggal_surat);
			view()->share('t_tunggakan', $t_tunggakan);
			view()->share('before', $before);
			view()->share('middle', $middle);
			view()->share('after', $after);
			view()->share('pimpinan', $pimpinan);

			if (str_is('surat_peringatan*', $surat->tag)) {
				view()->share('html', ['title' => 'Surat Peringatan | ' . config()->get('app.name') . '.com']);

				return view('v2.kredit.print.surat_peringatan');
			} else {
				view()->share('html', ['title' => 'Somasi | ' . config()->get('app.name') . '.com']);

				return view('v2.kredit.print.surat_somasi');
			}
		} catch (Exception $e) {
			return redirect()->back()->withErrors($e->getMessage());
		}
	}
}
