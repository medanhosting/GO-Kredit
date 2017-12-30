<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\Aktif;

use App\Service\Traits\IDRTrait;

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
	// 	$ad 		= AngsuranDetail::whereIn('angsuran_id', $ids)->where('tag', '<>', 'bunga')->sum('amount');

	// 	return self::formatMoneyto($ad);
	// }

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public static function potongan($nomor_kredit){
		//find kredit jangka_waktu
		$aktif 		= Aktif::where('nomor_kredit', $nomor_kredit)->first();

		//find last paid
		$kredit 	= AngsuranDetail::where('nomor_kredit', $nomor_kredit)->wherein('tag', ['pokok', 'bunga'])->wherenotnull('nota_bayar_id')->orderby('nth', 'desc')->first();
		$tb 		= AngsuranDetail::where('nomor_kredit', $nomor_kredit)->where('tag', 'bunga')->wherenull('nota_bayar_id')->sum('amount');
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