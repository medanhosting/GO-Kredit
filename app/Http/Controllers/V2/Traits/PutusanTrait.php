<?php

namespace App\Http\Controllers\V2\Traits;

use Thunderlabid\Pengajuan\Models\Putusan;
use Validator;

/**
 * Trait tanggal
 *
 * Digunakan untuk reformat tanggal sesuai kontrak
 *
 * @package    Thunderlabid
 * @subpackage Credit
 * @author     C Mooy <chelsy@thunderlab.od>
 */
trait PutusanTrait {
	private function checker_realisasi($checklists) 
	{
		//CHECKER Checklists
		$rule_n 	= Putusan::rule_of_checklist();
		$total 		= count($rule_n);

		if (count($checklists)) {
			$validator 	= Validator::make($checklists, $rule_n);

			if ($validator->fails()) {
				$complete	= (count($rule_n) - count($validator->messages()));
				$checker 	= false;
			} else {
				$complete	= count($rule_n);
				$checker 	= true;
			}
		} else {
			$checker 		= false;
		}

		$percentage 	= floor(($complete / max($total, 1)) * 100);

		view()->share('checker', $checker);
		view()->share('percentage', $percentage);
	}
}