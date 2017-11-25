<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Angsuran;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Service\Traits\IDRTrait;

class PelunasanAngsuran
{
	use IDRTrait;

	public static function instance() {
        return new PelunasanAngsuran();
    }
	
	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public static function hitung($nomor_kredit, $exlude_id = 0){
		$kredit 	= Angsuran::where('nomor_kredit', $nomor_kredit)->where('id', '<>', $exlude_id)->wherenull('paid_at')->get(['id']);

		$ids 		= array_column($kredit->toArray(), 'id');
		$ad 		= AngsuranDetail::whereIn('angsuran_id', $ids)->where('tag', '<>', 'bunga')->sum('amount');

		return self::formatMoneyto($ad);
	}
}