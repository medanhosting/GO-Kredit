<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;

use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use Thunderlabid\Pengajuan\Traits\IDRTrait;

class DashboardController extends Controller
{
	use IDRTrait;

	public function home() 
	{
		//atur menu scopes
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));
		
		$this->layout->pages 	= view('dashboard.overview');
		return $this->layout;
	}

	public function simulasi($mode) 
	{
		//atur menu scopes
		view()->share('kantor_aktif_id', request()->get('kantor_aktif_id'));

		$rincian 	= [];

		if(request()->has('kemampuan_angsur') && request()->has('pokok_pinjaman'))
		{
			if($mode=='pa')
			{
				$rincian['pokok_pinjaman']		= request()->get('pokok_pinjaman');
				$rincian['kemampuan_angsur']	= request()->get('kemampuan_angsur');

				if(request()->has('bunga_per_bulan'))
				{
					$rincian['bunga_per_bulan']	= round(request()->get('bunga_per_bulan'),2);
				}
				else
				{
					$rincian['bunga_per_bulan']	=  round((rand(100,500)/1000),2);
				}

				$k_angs 		= $this->formatMoneyFrom($rincian['kemampuan_angsur']);
				$p_pinjaman 	= $this->formatMoneyFrom($rincian['pokok_pinjaman']);

				//bunga tahunan
				$total_bunga  	= $p_pinjaman * $rincian['bunga_per_bulan'];
				$bulan 			= ceil(($p_pinjaman + $total_bunga)/$k_angs);

				//kredit diusulkan
				$kredit_update 	= $bulan * $k_angs;

				$rincian['lama_angsuran']	= $bulan;
				$rincian['provisi']			= $this->formatMoneyTo((0.5 * $p_pinjaman)/100);
				$rincian['administrasi']	= $this->formatMoneyTo(10000);
				$rincian['legal']			= $this->formatMoneyTo(50000);
				$rincian['pinjaman_bersih']	= $this->formatMoneyTo($p_pinjaman - ((0.5 * $p_pinjaman)/100) - 60000);
				$sisa_pinjaman 				= $p_pinjaman;

				foreach (range(1, $bulan) as $k) 
				{
					$angsuran_bulanan 	= min(ceil(($p_pinjaman/$bulan)/100) * 100, $sisa_pinjaman);
					$angsuran_bunga 	= floor(($total_bunga/$bulan)/100) * 100;

					$sisa_pinjaman 		= $sisa_pinjaman - $angsuran_bulanan;

					$rincian['angsuran'][$k]['bulan']			= Carbon::now()->addmonths($k)->format('M/Y');
					$rincian['angsuran'][$k]['angsuran_bunga']	= $this->formatMoneyTo($angsuran_bunga);
					$rincian['angsuran'][$k]['angsuran_pokok']	= $this->formatMoneyTo($angsuran_bulanan);
					$rincian['angsuran'][$k]['total_angsuran']	= $this->formatMoneyTo($angsuran_bulanan + $angsuran_bunga);
					$rincian['angsuran'][$k]['sisa_pinjaman']	= $this->formatMoneyTo($sisa_pinjaman);
				}
			}
			elseif($mode=='rumah_delta')
			{
				$rincian['pokok_pinjaman']		= request()->get('pokok_pinjaman');
				$rincian['kemampuan_angsur']	= request()->get('kemampuan_angsur');

				if(request()->has('bunga_per_bulan'))
				{
					$rincian['bunga_per_bulan']	= request()->get('bunga_per_bulan');
				}
				else
				{
					$rincian['bunga_per_bulan']	=  (rand(100,500)/1000);
				}

				$k_angs 		= $this->formatMoneyFrom($rincian['kemampuan_angsur']);
				$p_pinjaman 	= $this->formatMoneyFrom($rincian['pokok_pinjaman']);

				//bunga tahunan
				$total_bunga  	= $p_pinjaman * $rincian['bunga_per_bulan'];
				$bulan 			= ceil(($p_pinjaman + $total_bunga)/$k_angs);

				//kredit diusulkan
				$kredit_update 	= $bulan * $k_angs;

				$rincian['lama_angsuran']	= $bulan;
				$rincian['provisi']			= $this->formatMoneyTo((0.5 * $p_pinjaman)/100);
				$rincian['administrasi']	= $this->formatMoneyTo(10000);
				$rincian['legal']			= $this->formatMoneyTo(50000);
				$rincian['pinjaman_bersih']	= $this->formatMoneyTo($p_pinjaman - ((0.5 * $p_pinjaman)/100) - 60000);
				$sisa_pinjaman 				= $p_pinjaman;

				foreach (range(1, $bulan) as $k) 
				{
					$angsuran_bulanan 	= min(ceil(($p_pinjaman/$bulan)/100) * 100, $sisa_pinjaman);
					$angsuran_bunga 	= floor(($p_pinjaman - (($k-1) * $angsuran_bulanan)) * $rincian['bunga_per_bulan']);

					$sisa_pinjaman 		= $sisa_pinjaman - $angsuran_bulanan;

					$rincian['angsuran'][$k]['bulan']			= Carbon::now()->addmonths($k)->format('M/Y');
					$rincian['angsuran'][$k]['angsuran_bunga']	= $this->formatMoneyTo($angsuran_bunga);
					$rincian['angsuran'][$k]['angsuran_pokok']	= $this->formatMoneyTo($angsuran_bulanan);
					$rincian['angsuran'][$k]['total_angsuran']	= $this->formatMoneyTo($angsuran_bulanan + $angsuran_bunga);
					$rincian['angsuran'][$k]['sisa_pinjaman']	= $this->formatMoneyTo($sisa_pinjaman);
				}
			}
		}
		
		$this->layout->pages 	= view('dashboard.simulasi', compact('mode', 'rincian'));
		return $this->layout;
	}
}
