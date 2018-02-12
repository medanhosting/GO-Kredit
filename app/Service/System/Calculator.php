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

	public function potonganBefore($nk, Carbon $tanggal)
	{
		//find kredit jangka_waktu
		$aktif 		= Aktif::where('nomor_kredit', $nk)->first();

		//find angsuran terdekat
		$kredit 	= JadwalAngsuran::where('nomor_kredit', $nk)->where('tanggal', '>', $tanggal->endofday()->format('Y-m-d H:i:s'))->orderby('tanggal', 'asc')->first();

		if(str_is($aktif['jenis_pinjaman'], 'pa') && $kredit['nth'] < $aktif['jangka_waktu'])
		{
			$tb 		= self::formatMoneyFrom($kredit['bunga']) * ($aktif['jangka_waktu'] - ($kredit['nth']*1 - 1));
		}
		elseif(str_is($aktif['jenis_pinjaman'], 'pt') && $kredit['nth'] < $aktif['jangka_waktu'])
		{
			$tb 		= self::formatMoneyFrom($kredit['bunga']) * ($aktif['jangka_waktu'] - ($kredit['nth']*1 - 1));
		}else{
			$tb 		= 0;
		}

		//check pelunasan > 1/2 masa nth
		$max_jw 	= ceil($aktif['jangka_waktu']/2);

		if($kredit['nth'] <= 3){
			$potongan 	= 0.7 * $tb;
		}
		elseif($max_jw >= $kredit['nth']){
			$potongan 	= 0.5 * $tb;
		}else{
			$potongan 	= 0.2 * $tb;
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
		$aktif 		= Aktif::where('nomor_kredit', $nk)->first();
		$piutang 	= Calculator::piutangBefore($nk, $tanggal);

		return $piutang * ($aktif['persentasi_denda']/100) * 3;
	}
}