<?php

namespace App\Http\Controllers\V2\Traits;

use Thunderlabid\Finance\Models\COA;

/**
 * Trait tanggal
 *
 * Digunakan untuk reformat tanggal sesuai kontrak
 *
 * @package    Thunderlabid
 * @subpackage Credit
 * @author     C Mooy <chelsy@thunderlab.od>
 */
trait AkunTrait {
	
 	public function get_akun($kantor_id, $kode = null){
		$acc	= COA::where('kode_kantor', $kantor_id)->wherenotnull('coa_id');
 		if(!is_null($kode)){
 			$acc 	= $acc->where('nomor_perkiraan', $kode);
 		}
 		$acc 	= $acc->get();
		$akun	= [];

		foreach ($acc as $k => $v) {
			$akun[$v['nomor_perkiraan']]	= $v['akun'];
		}
		return $akun;
 	}
}