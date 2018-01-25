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
			'akun'				=> 'KAS'],
			['nomor_perkiraan'	=> '110.000',
			'akun'				=> 'ANTAR BANK AKTIVA'],
			['nomor_perkiraan'	=> '120.000',
			'akun'				=> 'PINJAMAN YANG DIBERIKAN'],
			['nomor_perkiraan'	=> '130.000',
			'akun'				=> 'PENYISIHAN PENGHAPUSAN AKTIVA PRODUKTIF'],
			['nomor_perkiraan'	=> '140.000',
			'akun'				=> 'PIUTANG BUNGA'],
			['nomor_perkiraan'	=> '150.000',
			'akun'				=> 'AKTIVA TETAP & INVENTARIS'],
			['nomor_perkiraan'	=> '160.000',
			'akun'				=> 'ANTAR KANTOR AKTIVA'],
			['nomor_perkiraan'	=> '170.000',
			'akun'				=> 'RUPA-RUPA AKTIVA'],
			['nomor_perkiraan'	=> '180.000',
			'akun'				=> 'AYAT SILANG (REKENING ANTARA)'],
		];

		$codes 	= [
			['nomor_perkiraan'	=> '100.100',
			'akun'				=> 'Kas Besar',
			'parent'			=> '100.000'],
			['nomor_perkiraan'	=> '100.200',
			'akun'				=> 'Kas Kecil',
			'parent'			=> '100.000'],
			['nomor_perkiraan'	=> '100.300',
			'akun'				=> 'Kas Titipan',
			'parent'			=> '100.000'],

			['nomor_perkiraan'	=> '140.600',
			'akun'				=> 'Piutang Denda',
			'parent'			=> '140.000'],
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

		foreach ($codes as $k => $v) {
			$parent 	= Account::where('kode_kantor', $model['id'])->where('nomor_perkiraan', $v['parent'])->first();
			$acc 		= Account::where('kode_kantor', $model['id'])->where('nomor_perkiraan', $v['nomor_perkiraan'])->first();

			if(!$acc){
				$acc	= new Account;
			}

			$acc->kode_kantor 			= $model['id'];
			$acc->nomor_perkiraan 		= $v['nomor_perkiraan'];
			$acc->akun 					= $v['akun'];
			$acc->akun_id 				= $parent->id;
			$acc->save();
		}
	}
}