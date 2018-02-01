<?php

namespace App\Http\Controllers\V2\Dashboard;

use Auth;
use Carbon\Carbon;

use Thunderlabid\Manajemen\Models\Kantor;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Kredit\Models\MutasiJaminan;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
	public function index() 
	{
		$today 		= Carbon::parse('2018-01-27');

		$data['total_unit']				= Kantor::count();
		$data['total_karyawan']			= PenempatanKaryawan::active($today)->where('kantor_id', request()->get('kantor_aktif_id'))->count();
		$data['total_pengajuan']		= Pengajuan::where('kode_kantor', request()->get('kantor_aktif_id'))->count();
		$data['total_kredit']			= Aktif::where('kode_kantor', request()->get('kantor_aktif_id'))->count();

		$data['list_survei']			= Pengajuan::where('kode_kantor', request()->get('kantor_aktif_id'))->status('survei')->skip(0)->take(2)->get();
		$data['list_realisasi']			= Pengajuan::where('kode_kantor', request()->get('kantor_aktif_id'))->status('setuju')->with(['putusan'])->skip(0)->take(5)->get();

		$data['list_angsuran']			= [];
		// $data['list_angsuran']			= JadwalAngsuran::wherehas('kredit', function($q){$q->kantor(request()->get('kantor_aktif_id'));})->where('tanggal', '>=', $today->startofday()->format('Y-m-d H:i:s'))->where('tanggal', '<=', $today->endofday()->format('Y-m-d H:i:s'))->displaying()->with(['kredit'])->skip(0)->take(5)->get();

		$data['list_jaminan_keluar']	= MutasiJaminan::where('tag', 'out')->where('status', 'completed')->wheredoesnthave('kredit.angsuran', function($q){$q->wherenotnull('nomor_faktur');})->with(['kredit'])->skip(0)->take(5)->get();
		$data['list_tunggakan'] 		= [];
		// $data['list_tunggakan'] 		= JadwalAngsuran::wherehas('kredit', function($q){$q->where('kode_kantor', request()->get('kantor_aktif_id'));})->HitungTunggakanBeberapaWaktuLalu($today)->with(['kredit'])->orderby('tanggal', 'asc')->skip(0)->take(5)->get();

		//atur menu scopes
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
		view()->share('html', ['title' => 'Dashboard · GO-Kredit.com']);
		
		$this->layout->pages 	= view('v2.dashboard.index', compact('data'));
		return $this->layout;
	}

	public function koperasi(){
		$url 	= request()->get('prev_url');

		view()->share('html', ['title' => 'Pilih Koperasi · GO-Kredit.com']);

		return view('dashboard.koperasi', compact('url'));
	}
}
