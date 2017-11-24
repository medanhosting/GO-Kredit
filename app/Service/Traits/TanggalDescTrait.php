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
trait TanggalDescTrait {

 	/**
	 * parse input tanggal
	 * @param d F Y $value 
	 * @return Y-m-d $value 
	 */
	public function formatDateFrom($value)
	{
		return Carbon::createFromFormat('d F Y', $value)->format('Y-m-d');
	}

	/**
	 * parse output tanggal
	 * @param Y-m-d $value 
	 * @return d F Y $value 
	 */
	public function formatDateTo($value)
	{
		return Carbon::parse($value)->format('d F Y');
	}
}