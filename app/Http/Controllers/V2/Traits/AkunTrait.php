<?php

namespace App\Http\Controllers\V2\Traits;

use Thunderlabid\Finance\Models\Account;

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
	
 	public function get_akun($kantor_id){
		$acc	= Account::where('kode_kantor', $kantor_id)->where('is_pasiva', false)->get();
		$akun	= [];

		foreach ($acc as $k => $v) {
			$akun[$v['kode_akun']]	= $v['akun'];
		}
		return $akun;
 	}
}