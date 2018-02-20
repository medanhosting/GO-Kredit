<?php

namespace App\Service\Traits;

/**
 * Trait Link list
 *
 * Digunakan untuk initizialize link list mode
 *
 * @package    TTagihan
 * @author     C Mooy <chelsymooy1108@gmail.com>
 */
trait JurnalTrait {

	public function get_akun_table(){
		$table_akun 	= [
			'piutang_pokok'		=> 	[ 
										'pa' => '120.300', 
										'pt' => '120.400' 
									],
			'piutang_bunga'		=> 	[ 
										'pa' => '140.100', 
										'pt' => '140.200' 
									],
			'piutang_denda'		=> 	[ 
										'pa' => '140.600', 
										'pt' => '140.600' 
									],
			'titipan'			=> 	[ 
										'pa' => '200.210', 
										'pt' => '200.210' 
									],
			'pokok'				=> 	[ 
										'pa' => '120.100', 
										'pt' => '120.200' 
									],
			'bunga'				=> 	[ 
										'pa' => '401.121', 
										'pt' => '401.122' 
									],
			'bunga_dipercepat'	=> 	[ 
										'pa' => '401.123', 
										'pt' => '401.123' 
									],
			'denda'				=> 	[ 
										'pa' => '401.301', 
										'pt' => '401.301' 
									],
			'kas_titipan'		=> 	[ 
										'pa' => '100.300', 
										'pt' => '100.300' 
									],
			'provisi'			=> 	[ 
										'pa' => '401.201', 
										'pt' => '401.201' 
									],
			'administrasi'		=> 	[ 
										'pa' => '401.202', 
										'pt' => '401.202' 
									],
			'legal'				=> 	[ 
										'pa' => '401.303', 
										'pt' => '401.303' 
									],
			'biaya_notaris'		=> 	[ 
										'pa' => '200.230', 
										'pt' => '200.230' 
									],

			'pyd_bunga'			=> 	[ 
										'pa' => '260.110', 
										'pt' => '260.110' 
									],
			'pyd_denda'			=> 	[ 
										'pa' => '260.120', 
										'pt' => '260.120' 
									],
		];

		return $table_akun;
	}
}