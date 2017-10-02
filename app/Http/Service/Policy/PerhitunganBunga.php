<?php

namespace App\Http\Service\Policy;

use Illuminate\Support\Str;
use Exception, Validator;
use Carbon\Carbon;

use Thunderlabid\Pengajuan\Traits\IDRTrait;

class PerhitunganBunga
{
	use IDRTrait;

	protected $pengajuan_kredit;
	protected $kemampuan_angsur;
	protected $bunga_per_bulan;

	/**
	 * Create a new job instance.
	 *
	 * @param  $file
	 * @return void
	 */
	public function __construct($pengajuan_kredit, $kemampuan_angsur, $bunga_per_bulan = null)
	{
		$this->pengajuan_kredit 	= $pengajuan_kredit;
		$this->kemampuan_angsur 	= $kemampuan_angsur;
		$this->bunga_per_bulan 		= $bunga_per_bulan;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function pa()
	{
		try
		{
			$rincian['pokok_pinjaman']		= $this->pengajuan_kredit;
			$rincian['kemampuan_angsur']	= $this->kemampuan_angsur;

			$k_angs 		= $this->formatMoneyFrom($rincian['kemampuan_angsur']);
			$p_pinjaman 	= $this->formatMoneyFrom($rincian['pokok_pinjaman']);

			$est_bulan 		= $p_pinjaman / ($k_angs - ($p_pinjaman*0.02*(1/12)));

			if($p_pinjaman < 25000000)
			{
				$rincian['bunga_per_bulan']	= ((ceil($est_bulan/6) * 0.05) + 1.70)/12; 
			}
			elseif($p_pinjaman < 50000000)
			{
				$rincian['bunga_per_bulan']	= ((ceil($est_bulan/6) * 0.05) + 1.60)/12; 
			}
			elseif($p_pinjaman < 100000000)
			{
				$rincian['bunga_per_bulan']	= ((ceil($est_bulan/6) * 0.05) + 1.50)/12; 
			}
			elseif($p_pinjaman < 200000000)
			{
				$rincian['bunga_per_bulan']	= ((ceil($est_bulan/6) * 0.05) + 1.40)/12; 
			}
			else
			{
				$rincian['bunga_per_bulan']	= ((ceil($est_bulan/6) * 0.05) + 1.30)/12; 
			}

			if(!is_null($this->bunga_per_bulan) && $this->bunga_per_bulan > 0)
			{
				$rincian['bunga_per_bulan']	= $this->bunga_per_bulan;
			}

			$rincian['bunga_per_tahun']		= $rincian['bunga_per_bulan'] * 12;

			//bunga tahunan
			$bulan 			= ceil($est_bulan);
			$total_bunga  	= $p_pinjaman * ($rincian['bunga_per_bulan']/100) * $bulan;
			// $bulan 			= ceil(($p_pinjaman + $total_bunga)/$k_angs);

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

			return $rincian;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	public function pt()
	{
		try
		{
			$rincian['pokok_pinjaman']		= $this->pengajuan_kredit;
			$rincian['kemampuan_angsur']	= $this->kemampuan_angsur;

			$k_angs 		= $this->formatMoneyFrom($rincian['kemampuan_angsur']);
			$p_pinjaman 	= $this->formatMoneyFrom($rincian['pokok_pinjaman']);

			if($p_pinjaman < 25000000)
			{
				$rincian['bunga_per_bulan']	= 2.80/6; 
			}
			elseif($p_pinjaman < 50000000)
			{
				$rincian['bunga_per_bulan']	= 2.70/6; 
			}
			elseif($p_pinjaman < 100000000)
			{
				$rincian['bunga_per_bulan']	= 2.60/6; 
			}
			elseif($p_pinjaman < 200000000)
			{
				$rincian['bunga_per_bulan']	= 2.50/6; 
			}
			else
			{
				$rincian['bunga_per_bulan']	= 2.40/6; 
			}

			if(!is_null($this->bunga_per_bulan) && $this->bunga_per_bulan > 0)
			{
				$rincian['bunga_per_bulan']	= $this->bunga_per_bulan;
			}

			$rincian['bunga_per_tahun']		= $rincian['bunga_per_bulan'] * 6;

			//bunga tahunan
			$bulan 			= 6;

			//kredit diusulkan
			$rincian['lama_angsuran']	= $bulan;
			$rincian['provisi']			= $this->formatMoneyTo((0.5 * $p_pinjaman)/100);
			$rincian['administrasi']	= $this->formatMoneyTo(10000);
			$rincian['legal']			= $this->formatMoneyTo(50000);
			$rincian['pinjaman_bersih']	= $this->formatMoneyTo($p_pinjaman - ((0.5 * $p_pinjaman)/100) - 60000);
			$sisa_pinjaman 				= $p_pinjaman;

			foreach (range(1, $bulan) as $k) 
			{
				$angsuran_bulanan 	= $p_pinjaman/6;
				$angsuran_bunga 	= $p_pinjaman * ($rincian['bunga_per_bulan']/100);

				$angsuran_bulanan 	= ceil($angsuran_bulanan/100) *100;
				$angsuran_bunga 	= ceil($angsuran_bunga/100) *100;

				$angsuran_bulanan 	= min($sisa_pinjaman, $angsuran_bulanan);

				$sisa_pinjaman 		= $sisa_pinjaman - $angsuran_bulanan;

				$rincian['angsuran'][$k]['bulan']			= Carbon::now()->addmonths($k)->format('M/Y');
				$rincian['angsuran'][$k]['angsuran_bunga']	= $this->formatMoneyTo($angsuran_bunga);
				$rincian['angsuran'][$k]['angsuran_pokok']	= $this->formatMoneyTo($angsuran_bulanan);
				$rincian['angsuran'][$k]['total_angsuran']	= $this->formatMoneyTo($angsuran_bulanan + $angsuran_bunga);
				$rincian['angsuran'][$k]['sisa_pinjaman']	= $this->formatMoneyTo($sisa_pinjaman);
			}

			return $rincian;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
}