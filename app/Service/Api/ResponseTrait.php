<?php

namespace App\Service\Api;

use Exception;
use Illuminate\Support\MessageBag;

/**
 *
 * @package    Thunderlab
 * @author     C Mooy <chelsymooy1108@gmail.com>
 */
trait ResponseTrait {
 	 	
	/**
	 * Add Event_list to queue
	 * @param [IEvent_list] $event_list 
	 */
	public function error_response($data, $e)
	{
		$error 	= [];
		$msg 	= $e->getMessage();
		if(!(string)$msg){
			$msg 	= $msg->toArray();
			foreach ($msg as $k => $v) {
				$deep 	= explode('.', $k);
				$too 	= null;
				foreach ($deep as $k2 => $v2) {
					if(is_null($too)){
						$too 	= $v2;
					}else{
						$too	= $too.'['.$v2.']';
					}
				}
				$error[$too]	= $v;
			}
		} 
		elseif ($msg instanceof MessageBag) {
			$msg 		= $msg->toArray();
			foreach ($msg as $k => $v) {
				$deep 	= explode('.', $k);
				$too 	= null;
				foreach ($deep as $k2 => $v2) {
					if(is_null($too)){
						$too 	= $v2;
					}else{
						$too	= $too.'['.$v2.']';
					}
				}
				$error[$too]	= $v;
			}
		}
		else{
			$error 	= $msg;
		}

		return response()->json(['status' => 0, 'data' => $data, 'error' => ['message' => $error]]);
	}
}