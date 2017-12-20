<?php

namespace Thunderlabid\Pengajuan\Traits;

use Exception;

/**
 * Trait Nik
 *
 * Disesuaikan dengan policy, hanya menerima nik jawa timur
 *
 * @package    Thunderlabid
 * @subpackage Credit
 * @author     C Mooy <chelsy@thunderlab.id>
 */
trait KantorTrait {

	public function scopeKantor($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn('kode_kantor', $variable);
		}

		return $query->where('kode_kantor', $variable);
	}
}