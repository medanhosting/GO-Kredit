<?php

namespace Thunderlabid\Kredit\Models\Traits;

use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\BuktiRealisasi;

/**
 * Trait Link list
 *
 * Digunakan untuk initizialize link list mode
 *
 * @package    TTagihan
 * @author     C Mooy <chelsymooy1108@gmail.com>
 */
trait FakturTrait {
 	 	
	/**
	 * Add Event_list to queue
	 * @param [IEvent_list] $event_list 
	 */
	public function generatenomorfaktur($nomor_kredit)
	{
		$get_paid 		= NotaBayar::where('nomor_kredit', $nomor_kredit)->count();

		$last_letter	= str_pad(($get_paid + 1), 3, '0', STR_PAD_LEFT);

		return $nomor_kredit.'-'.$last_letter;
	}

	/**
	 * Add Event_list to queue
	 * @param [IEvent_list] $event_list 
	 */
	public function generatenomortransaksi($nomor_kredit)
	{
		$get_paid 		= BuktiRealisasi::where('nomor_kredit', $nomor_kredit)->count();

		$last_letter	= str_pad(($get_paid + 1), 3, '0', STR_PAD_LEFT);

		return $nomor_kredit.'-'.$last_letter;
	}
}