<?php

namespace App\Service\System;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;

use Thunderlabid\Finance\Models\DetailTransaksi;

use Carbon\Carbon;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\JurnalTrait;

class AkunKreditPT implements AkunKreditInterface {

	use IDRTrait;
	use JurnalTrait;

	public function __construct(){
		$this->table 	= $this->get_akun_table();
	}

	public function pencairan(DetailTransaksi $model, Aktif $kredit){
		$jumlah[0]	= abs($this->formatMoneyFrom($model->jumlah));

		$kre[0] 	= $model->notabayar->nomor_rekening;
		$deb[0] 	= $this->table['pokok'][$kredit->jenis_pinjaman];

		return ['kre' => $kre, 'deb' => $deb, 'jumlah' => $jumlah];
	}
	
	public function setoran_pencairan(DetailTransaksi $model, Aktif $kredit){
		$jumlah[0]	= abs($this->formatMoneyFrom($model->jumlah));

		$deb[0] 	= $model->notabayar->nomor_rekening;
		$kre[0] 	= $this->table[$model->tag][$kredit->jenis_pinjaman];

		return ['kre' => $kre, 'deb' => $deb, 'jumlah' => $jumlah];
	}

	public function piutang_pokok(DetailTransaksi $model, Aktif $kredit){
		$jumlah[0]	= abs($this->formatMoneyFrom($model->jumlah));

		$deb[0] 	= $this->table['piutang_pokok'][$kredit->jenis_pinjaman];
		$kre[0] 	= $this->table['pokok'][$kredit->jenis_pinjaman];
	
		return ['kre' => $kre, 'deb' => $deb, 'jumlah' => $jumlah];
	}

	public function piutang_bunga(DetailTransaksi $model, Aktif $kredit){
		$jumlah[0]	= abs($this->formatMoneyFrom($model->jumlah));

		$deb[0] 	= $this->table['piutang_bunga'][$kredit->jenis_pinjaman];
		$kre[0] 	= $this->table['pyd_bunga'][$kredit->jenis_pinjaman];

		return ['kre' => $kre, 'deb' => $deb, 'jumlah' => $jumlah];
	}

	public function piutang_denda(DetailTransaksi $model, Aktif $kredit){
		$jumlah[0]	= abs($this->formatMoneyFrom($model->jumlah));

		$deb[0] 	= $this->table['piutang_denda'][$kredit->jenis_pinjaman];
		$kre[0] 	= $this->table['pyd_denda'][$kredit->jenis_pinjaman];
	
		return ['kre' => $kre, 'deb' => $deb, 'jumlah' => $jumlah];
	}

	public function bayar_titipan(DetailTransaksi $model, Aktif $kredit){
		$jumlah[0]	= abs($this->formatMoneyFrom($model->jumlah));

		$deb[0] 	= $model->notabayar->nomor_rekening;
		$kre[0] 	= $this->table['titipan'][$kredit->jenis_pinjaman];
	
		return ['kre' => $kre, 'deb' => $deb, 'jumlah' => $jumlah];
	}

	public function bayar_pokok(DetailTransaksi $model, Aktif $kredit){

		$jumlah		= abs($this->formatMoneyFrom($model->jumlah));
		$tanggal 	= Carbon::createFromFormat('d/m/Y H:i', $model->notabayar->tanggal)->adddays(1);
		$i 			= 0;

		$hutang 	= Calculator::hutangBefore($model->morph_reference_id, $tanggal);
		$piut_p 	= Calculator::piutangPokokBefore($model->morph_reference_id, $tanggal);
		$piut_b 	= Calculator::piutangBungaBefore($model->morph_reference_id, $tanggal);

		while ($jumlah > 0) {

			if($piut_p > 0 ){
				//pelunasan
				$deb[] 	= $model->notabayar->nomor_rekening;
				$kre[] 	= $this->table['piutang_pokok'][$kredit->jenis_pinjaman];
				$ttl[] 	= abs($jumlah);

				$jumlah = 0;
			}else{
				//turun pokok
				$deb[] 	= $model->notabayar->nomor_rekening;
				$kre[] 	= $this->table['pokok'][$kredit->jenis_pinjaman];
				$ttl[] 	= abs($jumlah);

				$jumlah = 0;
			}

			$i 		= $i+1;
		}

		return ['kre' => $kre, 'deb' => $deb, 'jumlah' => $ttl];
	}

	public function bayar_bunga(DetailTransaksi $model, Aktif $kredit){
		$jumlah		= abs($this->formatMoneyFrom($model->jumlah));
		$tanggal 	= Carbon::createFromFormat('d/m/Y H:i', $model->notabayar->tanggal)->adddays(1);
		$titipan 	= Calculator::titipanBefore($model->morph_reference_id, $tanggal);
	
		$piut_b 	= Calculator::piutangBungaBefore($model->morph_reference_id, $tanggal);

		$sisa_b 	= Calculator::pelunasanBungaBefore($nomor_kredit, $tanggal);

		$kre 		= [];
		$deb 		= [];
		$ttl 		= [];

		//bayar pokok jt
		if($titipan >= $jumlah && $piut_b > 0){
			$deb[] 	= $this->table['titipan'][$kredit->jenis_pinjaman];
			$kre[] 	= $this->table['piutang_bunga'][$kredit->jenis_pinjaman];
			$ttl[] 	= abs($jumlah);
		}elseif($piut_b > 0){
			$deb[] 	= $model->notabayar->nomor_rekening;
			$kre[] 	= $this->table['piutang_bunga'][$kredit->jenis_pinjaman];
			$ttl[] 	= abs($jumlah);
		}elseif($jumlah >= $sisa_b){
			$deb[] 	= $model->notabayar->nomor_rekening;
			$kre[] 	= $this->table['bunga'][$kredit->jenis_pinjaman];
			$ttl[] 	= abs($jumlah);
		}

		return ['kre' => $kre, 'deb' => $deb, 'jumlah' => $ttl];
	}

	public function bayar_denda(DetailTransaksi $model, Aktif $kredit){
		$jumlah		= abs($this->formatMoneyFrom($model->jumlah));
		$tanggal 	= Carbon::createFromFormat('d/m/Y H:i', $model->notabayar->tanggal)->adddays(1);

		if(str_is($model->tag, 'restitusi_denda')){
			$ttl[0]	= $jumlah;
			$deb[0]	= $this->table['pyd_denda'][$kredit->jenis_pinjaman];
			$kre[0]	= $this->table['piutang_denda'][$kredit->jenis_pinjaman];
		}else{
			$piut_d 		= Calculator::piutangDendaBefore($model->morph_reference_id, $tanggal);
			
			while ($jumlah > 0) {
	
				if($piut_d > 0){
					$deb[]	= $model->notabayar->nomor_rekening;
					$kre[] 	= $this->table['piutang_denda'][$kredit->jenis_pinjaman];
					$ttl[]	= min($piut_d, $jumlah);

					$piut_d = $piut_d - min($piut_d, $jumlah);
					$jumlah = $jumlah - min($piut_d, $jumlah);
				}else{
					//kalau kelebihan
					$deb[]	= $model->notabayar->nomor_rekening;
					$kre[] 	= $this->table['titipan'][$kredit->jenis_pinjaman];
					$ttl[]	= min($jumlah);
					$jumlah = 0;
				}
			}
		}

		return ['kre' => $kre, 'deb' => $deb, 'jumlah' => $ttl];
	}
}