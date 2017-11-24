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
trait IDRTrait {
 	 	
	/**
	 * Add Event_list to queue
	 * @param [IEvent_list] $event_list 
	 */
	public function formatMoneyFrom($value)
	{
		if(is_null($value)){
			return 0;
		}

		list($currency,$amount) 	= array_map('trim', explode(' ', $value));

		if(!str_is(strtolower($currency), 'rp') && !str_is(strtolower($currency), 'rp.'))
		{
			throw new Exception('rp', 1);
		}

		return (str_replace('.', '', $amount)) * 1;
	}

	/**
	 * Add Event_list to queue
	 * @param [IEvent_list] $event_list 
	 */
	public function formatMoneyTo($value)
	{
		if(is_null($value)){
			return 'Rp 0';
		}

		return 'Rp '.number_format($value,0, "," ,".");
	}

	 public static function instance() {
        return new IDRTrait();
    }
}