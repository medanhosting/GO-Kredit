<?php

namespace App\Service\Traits;

use Carbon\Carbon;
use Thunderlabid\Kredit\Models\Aktif;

/**
 * Trait Link list
 *
 * Digunakan untuk initizialize link list mode
 *
 * @package    TTagihan
 * @author     C Mooy <chelsymooy1108@gmail.com>
 */
trait KreditGeneratorTrait {
 
	protected function generateNomorKredit($model)
	{
		$first_letter       = $model->pengajuan->kode_kantor;
		
		if(str_is($model->pengajuan->analisa->jenis_pinjaman,'pa')){
			$first_letter 	= $first_letter.'.002.';
		}else{
			$first_letter 	= $first_letter.'.001.';
		}

		$first_letter       = $first_letter.Carbon::now()->format('Y').'.';

		$prev_data          = Aktif::where('nomor_kredit', 'like', $first_letter.'%')->orderby('nomor_kredit', 'desc')->first();

		if($prev_data)
		{
			$last_letter	= explode('.', $prev_data['nomor_kredit']);
			$last_letter	= ((int)$last_letter[3] * 1) + 1;
		}
		else
		{
			$last_letter	= 1;
		}

		$last_letter		= str_pad($last_letter, 4, '0', STR_PAD_LEFT);

		return $first_letter.$last_letter;
	}
}