<?php

namespace Thunderlabid\Finance\Models\Traits;

use Thunderlabid\Finance\Models\NotaBayar;

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
	public function generatenomorfaktur($morph_reference_id)
	{
		$get_paid 		= NotaBayar::where('nomor_faktur', 'like', $morph_reference_id.'%')->count();

		$last_letter	= str_pad(($get_paid + 1), 3, '0', STR_PAD_LEFT);

		return $morph_reference_id.'-'.$last_letter;
	}

	/**
	 * Add Event_list to queue
	 * @param [IEvent_list] $event_list 
	 */
	public function simplecrypt($string, $action = 'e')
	{
		$secret_key = env('QRCODE_KEY');
		$secret_iv 	= env('QRCODE_IV', 'LIABLE123');

		$output = false;
		$encrypt_method 	= "AES-256-CBC";
		$key 	= hash( 'sha256', $secret_key );
		$iv 	= substr( hash( 'sha256', $secret_iv ), 0, 16 );

		if( $action == 'e' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		}
		else if( $action == 'd' ){
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}

		return $output;
	}
}