<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Kredit\Models\Aktif;

use App\Service\Traits\IDRTrait;

use Carbon\Carbon;

class PelunasanAngsuran
{
	use IDRTrait;

	public static function instance() {
        return new PelunasanAngsuran();
    }
	
	// /**
	//  * Execute the job.
	//  *
	//  * @return void
	//  */
	// public static function hitung($nomor_kredit, $exlude_id = 0){
	// 	$kredit 	= Angsuran::where('nomor_kredit', $nomor_kredit)->where('id', '<>', $exlude_id)->wherenull('paid_at')->get(['id']);

	// 	$ids 		= array_column($kredit->toArray(), 'id');
	// 	$ad 		= JadwalAngsuran::whereIn('angsuran_id', $ids)->where('tag', '<>', 'bunga')->sum('amount');

	// 	return self::formatMoneyto($ad);
	// }

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public static function potongan($nomor_kredit){
		$today 		= Carbon::now();
		//find kredit jangka_waktu
		$aktif 		= Aktif::where('nomor_kredit', $nomor_kredit)->first();

		//find angsuran terdekat
		$kredit 	= JadwalAngsuran::where('nomor_kredit', $nomor_kredit)->where('tanggal', '>', $today->endofday()->format('Y-m-d H:i:s'))->orderby('nth', 'asc')->first();

		if(str_is($aktif['jenis_pinjaman'], 'pa'))
		{
			$rincian 	= new PerhitunganBunga($aktif['plafon_pinjaman'], 'Rp 0', $aktif['suku_bunga'], null, null, null, $aktif['jangka_waktu']);
			$rincian 	= $rincian->pa();

			$tb 		= self::formatMoneyFrom($rincian['angsuran'][$kredit['nth']]['angsuran_bunga']) * ($aktif['jangka_waktu'] - ($kredit['nth']*1 - 1));
		}
		elseif(str_is($aktif['jenis_pinjaman'], 'pt') && $kredit['nth'] < $aktif['jangka_waktu'])
		{
			$rincian 	= new PerhitunganBunga($aktif['plafon_pinjaman'], 'Rp 0', $aktif['suku_bunga'], null, null, null, $aktif['jangka_waktu']);
			$rincian 	= $rincian->pt();
			$tb 		= self::formatMoneyFrom($rincian['angsuran'][$kredit['nth']]['angsuran_bunga']) * ($aktif['jangka_waktu'] - ($kredit['nth']*1 - 1));
		}else{
			$tb 	= 0;
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

		return self::formatMoneyto(0 - $potongan);
	}
}