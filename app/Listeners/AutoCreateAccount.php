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
			['nomor_perkiraan'	=> '210.000',
			'akun'				=> 'TABUNGAN'],
			['nomor_perkiraan'	=> '220.000',
			'akun'				=> 'DEPOSITO BERJANGKA'],
			['nomor_perkiraan'	=> '230.000',
			'akun'				=> 'REKENING DIBLOKIR (TABUNGAN/DEPOSITO)'],
			['nomor_perkiraan'	=> '240.000',
			'akun'				=> 'ANTAR BANK PASIVA'],
			['nomor_perkiraan'	=> '250.000',
			'akun'				=> 'ANTAR KANTOR PASIVA'],
			['nomor_perkiraan'	=> '260.000',
			'akun'				=> 'PENDAPATAN YANG DITANGGUHKAN'],
			['nomor_perkiraan'	=> '270.000',
			'akun'				=> 'RUPA-RUPA PASIVA'],
			['nomor_perkiraan'	=> '280.000',
			'akun'				=> 'HUTANG LAINNYA'],
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
			'akun'				=> 'Kas Titipan (Sementara)',
			'parent'			=> '100.000'],

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
			['nomor_perkiraan'	=> '120.500',
			'akun'				=> 'Pinjaman Karyawan',
			'parent'			=> '120.000'],
			['nomor_perkiraan'	=> '120.600',
			'akun'				=> 'Pinjaman lainnya',
			'parent'			=> '120.000'],

			['nomor_perkiraan'	=> '130.100',
			'akun'				=> 'PPAP Pinjaman yang diberikan(Kredit)',
			'parent'			=> '130.000'],
			['nomor_perkiraan'	=> '130.200',
			'akun'				=> 'PPAP Penempatan dana pada Bank Umum atau Bank Lainnya',
			'parent'			=> '130.000'],

			['nomor_perkiraan'	=> '140.100',
			'akun'				=> 'P.Bunga Pinjaman Angsuran',
			'parent'			=> '140.000'],
			['nomor_perkiraan'	=> '140.200',
			'akun'				=> 'P.Bunga Pinjaman Tetap',
			'parent'			=> '140.000'],
			['nomor_perkiraan'	=> '140.300',
			'akun'				=> 'P.Bunga Jasa Giro',
			'parent'			=> '140.000'],
			['nomor_perkiraan'	=> '140.400',
			'akun'				=> 'P.Bunga Tabungan pd  Bank Umum/Bank/BPR/Koperasi Lain',
			'parent'			=> '140.000'],
			['nomor_perkiraan'	=> '140.500',
			'akun'				=> 'P.Bunga Deposito pd Bank Umum/Bank/BPR/Koperasi Lain',
			'parent'			=> '140.000'],
			['nomor_perkiraan'	=> '140.600',
			'akun'				=> 'P.Denda',
			'parent'			=> '140.000'],
			['nomor_perkiraan'	=> '140.700',
			'akun'				=> 'P.Lainnya ',
			'parent'			=> '140.000'],

			['nomor_perkiraan'	=> '150.100',
			'akun'				=> 'Tanah dan Bangunan',
			'parent'			=> '150.000'],
			['nomor_perkiraan'	=> '150.200',
			'akun'				=> 'Kendaraan',
			'parent'			=> '150.000'],
			['nomor_perkiraan'	=> '150.300',
			'akun'				=> 'Inventaris',
			'parent'			=> '150.000'],
			['nomor_perkiraan'	=> '150.400',
			'akun'				=> '(Akumulasi Penyusutan tanah & Bangunan)',
			'parent'			=> '150.000'],
			['nomor_perkiraan'	=> '150.500',
			'akun'				=> '(Akumulasi Penyusutan Kendaraan)',
			'parent'			=> '150.000'],
			['nomor_perkiraan'	=> '150.600',
			'akun'				=> '(Akumulasi Penyusutan Inventaris)',
			'parent'			=> '150.000'],
			['nomor_perkiraan'	=> '150.700',
			'akun'				=> 'Aktiva tidak berwujud',
			'parent'			=> '150.000'],
			['nomor_perkiraan'	=> '150.800',
			'akun'				=> '(Akumulasi penyusutan aktiva tidak berwujud )',
			'parent'			=> '150.000'],

			['nomor_perkiraan'	=> '160.100',
			'akun'				=> 'Tagihan pada Kantor Cabang',
			'parent'			=> '160.000'],

			['nomor_perkiraan'	=> '170.100',
			'akun'				=> 'Uang Muka Pajak',
			'parent'			=> '170.000'],
			['nomor_perkiraan'	=> '170.200',
			'akun'				=> 'Biaya Pra operasional/Biaya Pendirian',
			'parent'			=> '170.000'],
			['nomor_perkiraan'	=> '170.300',
			'akun'				=> 'Biaya Dibayar Dimuka',
			'parent'			=> '170.000'],
			['nomor_perkiraan'	=> '170.400',
			'akun'				=> 'Agunan Yang Diambil Alih',
			'parent'			=> '170.000'],
			['nomor_perkiraan'	=> '170.500',
			'akun'				=> 'Persediaan',
			'parent'			=> '170.000'],
			['nomor_perkiraan'	=> '170.600',
			'akun'				=> 'Uang Muka Biaya',
			'parent'			=> '170.000'],
			['nomor_perkiraan'	=> '170.700',
			'akun'				=> 'Bunga Dibayar Dimuka',
			'parent'			=> '170.000'],
			['nomor_perkiraan'	=> '170.800',
			'akun'				=> 'Rekening Selisih',
			'parent'			=> '170.000'],
			['nomor_perkiraan'	=> '170.900',
			'akun'				=> 'Rupa-Rupa Aktiva Lainnya',
			'parent'			=> '170.000'],

			['nomor_perkiraan'	=> '170.110',
			'akun'				=> 'UMP PPh Pasal 25',
			'parent'			=> '170.100'],
			['nomor_perkiraan'	=> '170.120',
			'akun'				=> 'UMP Pajak lainnya',
			'parent'			=> '170.100'],
			['nomor_perkiraan'	=> '170.210',
			'akun'				=> 'Biaya Pra operasional/Biaya Pendirian',
			'parent'			=> '170.200'],
			['nomor_perkiraan'	=> '170.220',
			'akun'				=> '(Amortisasi Biaya)',
			'parent'			=> '170.200'],
			['nomor_perkiraan'	=> '170.310',
			'akun'				=> 'BDD Pemeliharaan dan Perbaikan',
			'parent'			=> '170.300'],
			['nomor_perkiraan'	=> '170.320',
			'akun'				=> 'BDD Sewa',
			'parent'			=> '170.300'],
			['nomor_perkiraan'	=> '170.330',
			'akun'				=> 'BDD Provisi',
			'parent'			=> '170.300'],
			['nomor_perkiraan'	=> '170.340',
			'akun'				=> 'BDD Proses Peradilan (Penyls. Kredit Bermasalah)',
			'parent'			=> '170.300'],
			['nomor_perkiraan'	=> '170.350',
			'akun'				=> 'BDD Penyelesaian Kredit Bermasalah (Penagihan)',
			'parent'			=> '170.300'],
			['nomor_perkiraan'	=> '170.360',
			'akun'				=> 'BDD Lainnya',
			'parent'			=> '170.300'],
			['nomor_perkiraan'	=> '170.410',
			'akun'				=> 'AYDA Tanah',
			'parent'			=> '170.400'],
			['nomor_perkiraan'	=> '170.420',
			'akun'				=> 'AYDA Tanah & Bangunan',
			'parent'			=> '170.400'],
			['nomor_perkiraan'	=> '170.430',
			'akun'				=> 'AYDA Kendaraan Bermotor',
			'parent'			=> '170.400'],
			['nomor_perkiraan'	=> '170.440',
			'akun'				=> 'AYDA Lainnya',
			'parent'			=> '170.400'],
			['nomor_perkiraan'	=> '170.510',
			'akun'				=> 'Persediaan Materai dan Segel',
			'parent'			=> '170.500'],
			['nomor_perkiraan'	=> '170.520',
			'akun'				=> 'Persediaan ATK',
			'parent'			=> '170.500'],
			['nomor_perkiraan'	=> '170.530',
			'akun'				=> 'Persediaan Percetakan',
			'parent'			=> '170.500'],
			['nomor_perkiraan'	=> '170.540',
			'akun'				=> 'Persediaan Lainnya',
			'parent'			=> '170.500'],
			['nomor_perkiraan'	=> '170.610',
			'akun'				=> 'Uang Muka Biaya',
			'parent'			=> '170.600'],
			['nomor_perkiraan'	=> '170.710',
			'akun'				=> 'Bunga Dibayar Dimuka Deposito',
			'parent'			=> '170.700'],
			['nomor_perkiraan'	=> '170.720',
			'akun'				=> 'Bunga Dibayar Dimuka Lainnya',
			'parent'			=> '170.700'],
			['nomor_perkiraan'	=> '170.810',
			'akun'				=> 'Selisih Kas',
			'parent'			=> '170.800'],
			['nomor_perkiraan'	=> '170.820',
			'akun'				=> 'Selisih Pembulatan',
			'parent'			=> '170.800'],
			['nomor_perkiraan'	=> '170.830',
			'akun'				=> 'Selisih Lainnya',
			'parent'			=> '170.800'],

			['nomor_perkiraan'	=> '180.100',
			'akun'				=> 'Pemindahbukuan (Dana Kas)',
			'parent'			=> '180.000'],
			['nomor_perkiraan'	=> '180.200',
			'akun'				=> 'Pemindahbukuan (Setor Bank)',
			'parent'			=> '180.000'],
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