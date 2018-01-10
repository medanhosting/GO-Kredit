<?php

namespace App\Service\Traits;

use Exception;

/**
 * Trait Link list
 *
 * Digunakan untuk initizialize link list mode
 *
 * @package    TTagihan
 * @author     C Mooy <chelsymooy1108@gmail.com>
 */
trait TerbilangTrait {
 	 	
	/**
	 * Add Event_list to queue
	 * @param [IEvent_list] $event_list 
	 */
	public static function terbilang($x)
	{
		$arr = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

	    if ($x < 12)
			return "" . $arr[$x] . "";
	    elseif ($x < 20)
			return self::terbilang($x - 10) . " belas ";
	    elseif ($x < 100)
			return self::terbilang($x / 10) . " puluh " . self::terbilang($x % 10);
	    elseif ($x < 200)
			return "seratus " . self::terbilang($x - 100);
	    elseif ($x < 1000)
			return self::terbilang($x / 100) . " ratus " . self::terbilang($x % 100);
	    elseif ($x < 2000)
			return "seribu " . self::terbilang($x - 1000);
	    elseif ($x < 1000000)
			return self::terbilang($x / 1000) . " ribu " . self::terbilang($x % 1000);
	    elseif ($x < 1000000000)
			return self::terbilang($x / 1000000) . " juta " . self::terbilang($x % 1000000);
	}
}