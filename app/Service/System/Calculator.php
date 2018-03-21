<?php

namespace App\Service\System;

use Carbon\Carbon;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\JurnalTrait;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Finance\Models\Jurnal;

Class Calculator {

	use IDRTrait;
	use JurnalTrait;

	public static function instance() {
        return new Calculator();
    }

	public function hutangExactlyBefore($nk, Carbon $tanggal)
	{
		$total = Jurnal::where('morph_reference_id', $nk)->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', [Calculator::get_akun_table()['pokok']['pa'], Calculator::get_akun_table()['pokok']['pt'], Calculator::get_akun_table()['piutang_pokok']['pa'], Calculator::get_akun_table()['piutang_pokok']['pt'], Calculator::get_akun_table()['piutang_bunga']['pa'], Calculator::get_akun_table()['piutang_bunga']['pt']]);})->where('tanggal', '<=', $tanggal->format('Y-m-d H:i:s'))->sum('jumlah');

		return $total;
	}
 	 	
	public function hutangBefore($nk, Carbon $tanggal)
	{
		$total = Jurnal::where('morph_reference_id', $nk)->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', [Calculator::get_akun_table()['pokok']['pa'], Calculator::get_akun_table()['pokok']['pt'], Calculator::get_akun_table()['piutang_pokok']['pa'], Calculator::get_akun_table()['piutang_pokok']['pt'], Calculator::get_akun_table()['piutang_bunga']['pa'], Calculator::get_akun_table()['piutang_bunga']['pt']]);})->where('tanggal', '<', $tanggal->startofday()->format('Y-m-d H:i:s'))->sum('jumlah');

		return $total;
	}
 	 	
	public function titipanBefore($nk, Carbon $tanggal)
	{
		$total = Jurnal::where('morph_reference_id', $nk)->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', [Calculator::get_akun_table()['titipan']['pa'], Calculator::get_akun_table()['titipan']['pt']]);})->where('tanggal', '<', $tanggal->startofday()->format('Y-m-d H:i:s'))->sum('jumlah');

		return $total * -1;
	}

	public function titipanExactlyBefore($nk, Carbon $tanggal)
	{
		$total = Jurnal::where('morph_reference_id', $nk)->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', [Calculator::get_akun_table()['titipan']['pa'], Calculator::get_akun_table()['titipan']['pt']]);})->where('tanggal', '<', $tanggal->format('Y-m-d H:i:s'))->sum('jumlah');

		return $total * -1;
	}
 	 	
	public function piutangBefore($nk, Carbon $tanggal)
	{
		$total = Jurnal::where('morph_reference_id', $nk)->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', [Calculator::get_akun_table()['piutang_pokok']['pa'], Calculator::get_akun_table()['piutang_pokok']['pt'], Calculator::get_akun_table()['piutang_bunga']['pa'], Calculator::get_akun_table()['piutang_bunga']['pt']]);})->where('tanggal', '<', $tanggal->startofday()->format('Y-m-d H:i:s'))->sum('jumlah');

		return $total;
	}

	public function piutangPokokBefore($nk, Carbon $tanggal)
	{
		$total = Jurnal::where('morph_reference_id', $nk)->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', [Calculator::get_akun_table()['piutang_pokok']['pa'], Calculator::get_akun_table()['piutang_pokok']['pt']]);})->where('tanggal', '<', $tanggal->startofday()->format('Y-m-d H:i:s'))->sum('jumlah');

		return $total;
	}

	public function piutangBungaBefore($nk, Carbon $tanggal)
	{
		$total = Jurnal::where('morph_reference_id', $nk)->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', [Calculator::get_akun_table()['piutang_bunga']['pa'], Calculator::get_akun_table()['piutang_bunga']['pt']]);})->where('tanggal', '<', $tanggal->startofday()->format('Y-m-d H:i:s'))->sum('jumlah');

		return $total;
	}

	public function piutangDendaBefore($nk, Carbon $tanggal)
	{
		$total = Jurnal::where('morph_reference_id', $nk)->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', [Calculator::get_akun_table()['piutang_denda']['pa'], Calculator::get_akun_table()['piutang_denda']['pt']]);})->where('tanggal', '<', $tanggal->startofday()->format('Y-m-d H:i:s'))->sum('jumlah');

		return $total;
	}

	public function potonganBefore($nk, Carbon $tanggal)
	{
		//find kredit jangka_waktu
		$aktif 		= Aktif::where('nomor_kredit', $nk)->first();

		//find angsuran terdekat
		$kredit 	= JadwalAngsuran::where('nomor_kredit', $nk)->where('tanggal', '>', $tanggal->endofday()->format('Y-m-d H:i:s'))->orderby('tanggal', 'asc')->selectraw('*')->selectraw('bunga as total_bunga')->selectraw('tanggal as tanggal_akhir')->first();

		if(str_is($aktif['jenis_pinjaman'], 'pa') && $kredit['nth'] < $aktif['jangka_waktu'])
		{
			$tb 		= self::formatMoneyFrom($kredit['bunga']) * ($aktif['jangka_waktu'] - ($kredit['nth']*1 - 1));
			//check pelunasan > 1/2 masa nth
			$max_jw 	= round($aktif['jangka_waktu']/2);

			if($kredit['nth'] <= 4){
				//a. kalau angsuran berikut sebelum JT4
				$potongan 	= 0.7 * $tb;
			}
			elseif($max_jw >= $kredit['nth']){
				//b. kalau angsuran berikut sebelum JT separuh angsuran
				$potongan 	= 0.5 * $tb;
			}else{
				//c. else
				$potongan 	= 0.2 * $tb;
			}
		}
		elseif(str_is($aktif['jenis_pinjaman'], 'pt') && $kredit['nth'] < $aktif['jangka_waktu'])
		{
			$tb 		= self::formatMoneyFrom($kredit['bunga']) * ($aktif['jangka_waktu'] - ($kredit['nth']*1 - 1));

			$harian 	= (ceil($kredit['total_bunga']/3000) * 100);
			$hari 		= Carbon::parse($tanggal)->diffInDays(Carbon::parse($kredit['tanggal_akhir'])->submonthsnooverflow(1));

			$potongan 	= $tb - ($harian * $hari);
		}else{
			$potongan 	= 0;
		}

		return $potongan;
	}

	public function dendaBefore($nk, Carbon $tanggal)
	{
		$total = Jurnal::where('morph_reference_id', $nk)->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', [Calculator::get_akun_table()['piutang_denda']['pa'], Calculator::get_akun_table()['piutang_denda']['pt']]);})->where('tanggal', '<', $tanggal->startofday()->format('Y-m-d H:i:s'))->sum('jumlah');

		return $total;
	}

	public function restitusi3DBefore($nk, Carbon $tanggal)
	{
		$aktif		= Aktif::where('nomor_kredit', $nk)->first();
		// $denda 		= Calculator::dendaBefore($nk, $tanggal);
		$angs_r 	= JadwalAngsuran::where('nomor_kredit', $nk)->where('nth', '<', 2)->sum('jumlah');

		return $angs_r * ($aktif['persentasi_denda']/100) * 3;
	}

	public function totalBungaBefore($nk, Carbon $tanggal)
	{
		$total 		= JadwalAngsuran::where('nomor_kredit', $nk)->where('tanggal', '>=', $tanggal->format('Y-m-d H:i:s'))->sum('bunga');

		return $total;
	}


	public function pelunasanBefore($nk, Carbon $tanggal)
	{
		$hutang 	= Calculator::hutangBefore($nk, $tanggal);
		$piutang 	= Calculator::piutangBefore($nk, $tanggal);
		$bunga 		= Calculator::totalBungaBefore($nk, $tanggal);
		$potongan 	= Calculator::potonganBefore($nk, $tanggal);

		return ($hutang - $piutang) + ($bunga - $potongan);
	}


	public function pelunasanBungaBefore($nk, Carbon $tanggal)
	{
		$bunga 		= Calculator::totalBungaBefore($nk, $tanggal);
		$potongan 	= Calculator::potonganBefore($nk, $tanggal);

		return $bunga - $potongan;
	}
}