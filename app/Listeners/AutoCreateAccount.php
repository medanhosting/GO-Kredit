<?php

namespace App\Listeners;

use Thunderlabid\Finance\Models\Account;

class AutoCreateAccount
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle event
	 * @param  MODEL PUTUSAN $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$pcodes 	= [
			['nomor_perkiraan'	=> '100.000',
			'akun'				=> 'Kas'],
			['nomor_perkiraan'	=> '110.000',
			'akun'				=> 'Antar Bank Aktiva'],
		];

		$codes 	= [
			['nomor_perkiraan'	=> '100.100',
			'akun'				=> 'Kas Besar'],
			['nomor_perkiraan'	=> '100.200',
			'akun'				=> 'Kas Kecil'],
			['nomor_perkiraan'	=> '100.300',
			'akun'				=> 'Kas Titipan']
		];

		$model 	= $event->data;
		
		foreach ($pcodes as $k => $v) {
			$acc 		= Account::where('kode_kantor', $model['id'])->where('nomor_perkiraan', $v['nomor_perkiraan'])->first();

			if(!$acc){
				$acc	= new Account;
			}

			$acc->kode_kantor 			= $model['id'];
			$acc->nomor_perkiraan 		= $v['nomor_perkiraan'];
			$acc->akun 					= $v['akun'];
			$acc->save();
		}

		$data 	= Account::where('kode_kantor', $model['id'])->where('nomor_perkiraan', '100.000')->first();

		foreach ($codes as $k => $v) {
			$acc 		= Account::where('kode_kantor', $model['id'])->where('nomor_perkiraan', $v['nomor_perkiraan'])->first();

			if(!$acc){
				$acc	= new Account;
			}

			$acc->kode_kantor 			= $model['id'];
			$acc->nomor_perkiraan 		= $v['nomor_perkiraan'];
			$acc->akun 					= $v['akun'];
			$acc->akun_id 				= $data->id;
			$acc->save();
		}
	}
}