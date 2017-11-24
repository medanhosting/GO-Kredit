<?php

namespace App\Service\Traits;

use Carbon\Carbon;

/**
 * Trait tanggal
 *
 * Digunakan untuk reformat tanggal sesuai kontrak
 *
 * @package    Thunderlabid
 * @subpackage Credit
 * @author     C Mooy <chelsy@thunderlab.od>
 */
trait BulanDescTrait {

 	/**
	 * parse input tanggal
	 * @param F Y $value 
	 * @return Y-m-d $value 
	 */
	public function formatMonthFrom($value)
	{
		return Carbon::createFromFormat('F Y', $value)->format('Y-m');
	}

	/**
	 * parse output tanggal
	 * @param Y-m-d $value 
	 * @return F Y $value 
	 */
	public function formatMonthTo($value)
	{
		return Carbon::parse($value)->format('F Y');
	}
}