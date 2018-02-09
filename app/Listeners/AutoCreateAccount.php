<?php

namespace App\Listeners;

use Thunderlabid\Finance\Models\COA;

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
			['nomor_perkiraan'	=> '200.000',
			'akun'				=> 'KEWAJIBAN'],
			['nomor_perkiraan'	=> '260.000',
			'akun'				=> 'PENDAPATAN YANG DITANGGUHKAN'],
			['nomor_perkiraan'	=> '400.000',
			'akun'				=> 'PENDAPATAN'],
		];

		$codes 	= [
			['nomor_perkiraan'	=> '100.100',
			'akun'				=> 'Kas Besar',
			'parent'			=> '100.000'],
			['nomor_perkiraan'	=> '100.110',
			'akun'				=> 'Teller A',
			'parent'			=> '100.100'],
			['nomor_perkiraan'	=> '100.120',
			'akun'				=> 'Teller B',
			'parent'			=> '100.100'],
			['nomor_perkiraan'	=> '100.200',
			'akun'				=> 'Kas Kecil',
			'parent'			=> '100.000'],
			['nomor_perkiraan'	=> '100.300',
			'akun'				=> 'Kas Titipan',
			'parent'			=> '100.000'],
			['nomor_perkiraan'	=> '100.310',
			'akun'				=> 'Kolektor',
			'parent'			=> '100.300'],


			['nomor_perkiraan'	=> '120.100',
			'akun'				=> 'Pinjaman Angsuran',
			'parent'			=> '120.000'],
			['nomor_perkiraan'	=> '120.200',
			'akun'				=> 'Pinjaman Tetap',
			'parent'			=> '120.000'],
			['nomor_perkiraan'	=> '120.300',
			'akun'				=> 'Pokok PA Jatuh Tempo',
			'parent'			=> '120.000'],
			['nomor_perkiraan'	=> '120.400',
			'akun'				=> 'Pokok PT Jatuh Tempo',
			'parent'			=> '120.000'],


			['nomor_perkiraan'	=> '140.100',
			'akun'				=> 'Piutang Bunga Pinjaman Angsuran',
			'parent'			=> '140.000'],
			['nomor_perkiraan'	=> '140.200',
			'akun'				=> 'Piutang Bunga Pinjaman Tetap',
			'parent'			=> '140.000'],

			['nomor_perkiraan'	=> '140.600',
			'akun'				=> 'Piutang Denda',
			'parent'			=> '140.000'],

			['nomor_perkiraan'	=> '200.200',
			'akun'				=> 'Titipan',
			'parent'			=> '200.000'],
			['nomor_perkiraan'	=> '200.210',
			'akun'				=> 'Titipan Angsuran',
			'parent'			=> '200.200'],

			['nomor_perkiraan'	=> '200.230',
			'akun'				=> 'Titipan Biaya Notaris',
			'parent'			=> '200.200'],

			['nomor_perkiraan'	=> '260.110',
			'akun'				=> 'PYD Bunga',
			'parent'			=> '260.000'],
			['nomor_perkiraan'	=> '260.120',
			'akun'				=> 'PYD Denda',
			'parent'			=> '260.000'],


			['nomor_perkiraan'	=> '401.000',
			'akun'				=> 'Pendapatan Operasional',
			'parent'			=> '400.000'],
			
			['nomor_perkiraan'	=> '401.100',
			'akun'				=> 'Pendapatan Bunga',
			'parent'			=> '401.000'],
			['nomor_perkiraan'	=> '401.120',
			'akun'				=> 'Pihak Ketiga Bukan Bank',
			'parent'			=> '401.000'],
			['nomor_perkiraan'	=> '401.121',
			'akun'				=> 'Bunga Pinjaman Angsuran',
			'parent'			=> '401.120'],
			['nomor_perkiraan'	=> '401.122',
			'akun'				=> 'Bunga Pinjaman Tetap',
			'parent'			=> '401.120'],

			['nomor_perkiraan'	=> '401.200',
			'akun'				=> 'Provisi dan Administrasi',
			'parent'			=> '401.000'],
			['nomor_perkiraan'	=> '401.201',
			'akun'				=> 'Provisi',
			'parent'			=> '401.200'],
			['nomor_perkiraan'	=> '401.202',
			'akun'				=> 'Administrasi Pinjaman',
			'parent'			=> '401.200'],
			['nomor_perkiraan'	=> '401.204',
			'akun'				=> 'Legalitas',
			'parent'			=> '401.200'],
			['nomor_perkiraan'	=> '401.303',
			'akun'				=> 'Legal',
			'parent'			=> '401.300'],
			['nomor_perkiraan'	=> '401.305',
			'akun'				=> 'Bunga & Denda yang Dihapusbukukan',
			'parent'			=> '401.300'],
			['nomor_perkiraan'	=> '401.205',
			'akun'				=> 'Biaya Notaris',
			'parent'			=> '401.200'],
		];
		
		$model 	= $event->data;
		
		foreach ($pcodes as $k => $v) {
			$acc 		= COA::where('kode_kantor', $model['id'])->where('nomor_perkiraan', $v['nomor_perkiraan'])->first();

			if(!$acc){
				$acc	= new COA;
			}

			$acc->kode_kantor 			= $model['id'];
			$acc->nomor_perkiraan 		= $v['nomor_perkiraan'];
			$acc->akun 					= $v['akun'];
			$acc->save();
		}

		foreach ($codes as $k => $v) {
			$parent 	= COA::where('kode_kantor', $model['id'])->where('nomor_perkiraan', $v['parent'])->first();
			$acc 		= COA::where('kode_kantor', $model['id'])->where('nomor_perkiraan', $v['nomor_perkiraan'])->first();

			if(!$acc){
				$acc	= new COA;
			}

			$acc->kode_kantor 			= $model['id'];
			$acc->nomor_perkiraan 		= $v['nomor_perkiraan'];
			$acc->akun 					= $v['akun'];
			$acc->coa_id 				= $parent->id;
			$acc->save();
		}
	}
}