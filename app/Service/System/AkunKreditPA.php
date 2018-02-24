<?php

namespace App\Service\System;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;

use Thunderlabid\Finance\Models\DetailTransaksi;

use Carbon\Carbon;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\JurnalTrait;

class AkunKreditPA implements AkunKreditInterface {

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
		$titipan 	= Calculator::titipanBefore($model->morph_reference_id, $tanggal);
		$i 			= 0;

		while ($jumlah > 0) {
			if($titipan > 0){
				//kalau ada titipan
				$angs_1 	= JadwalAngsuran::where('nomor_kredit', $model->morph_reference_id)
				->orderby('nth', 'asc')
				->wherenull('nomor_faktur')
				->where('tanggal', '<', $tanggal->format('Y-m-d H:i:s'))
				->selectraw('pokok as piut_p')
				->selectraw('bunga as piut_b')
				->selectraw('jumlah as piut_t')
				->skip($i)
				->take(1)
				->first();

				if($titipan >= $angs_1['piut_p'] && $angs_1['piut_t'] > 0){
					//kalau ada angs JT
					$deb[] 	= $this->table['titipan'][$kredit->jenis_pinjaman];
					$kre[] 	= $this->table['piutang_pokok'][$kredit->jenis_pinjaman];
					$ttl[] 	= abs($angs_1['piut_p']);

					$titipan 	= $titipan - $angs_1['piut_t'];
				}elseif($angs_1['piut_t'] > 0){
					//get them balance
					$deb[] 		= $model->notabayar->nomor_rekening;
					$kre[] 		= $this->table['titipan'][$kredit->jenis_pinjaman];
					$ttl[] 		= abs($angs_1['piut_t'] - $titipan);
					
					//bayar angsuran
					$deb[] 	= $this->table['titipan'][$kredit->jenis_pinjaman];
					$kre[] 	= $this->table['piutang_pokok'][$kredit->jenis_pinjaman];
					$ttl[] 	= abs($angs_1['piut_p']);

					$jumlah 	= $jumlah - (abs($angs_1['piut_p'] - $titipan));
					$titipan 	= 0;
				}else{
					//bayar angsuran
					$deb[] 		= $model->notabayar->nomor_rekening;
					$kre[] 		= $this->table['titipan'][$kredit->jenis_pinjaman];
					$ttl[] 		= $jumlah;
				}
			}else{
				//kalau tidak ada titipan
				$angs_1 	= JadwalAngsuran::where('nomor_kredit', $model->morph_reference_id)
				->orderby('nth', 'asc')
				->wherenull('nomor_faktur')
				->where('tanggal', '<', $tanggal->format('Y-m-d H:i:s'))
				->selectraw('pokok as piut_p')
				->selectraw('bunga as piut_b')
				->selectraw('jumlah as piut_t')
				->skip($i)
				->take(1)
				->first();
			
				$piut_p 	= Calculator::piutangPokokBefore($model->morph_reference_id, $tanggal);
				$piut_b 	= Calculator::piutangBungaBefore($model->morph_reference_id, $tanggal);
				$hutang 	= Calculator::hutangBefore($model->morph_reference_id, $tanggal);

				if($angs_1['piut_t'] > 0){
					//kalau ada piutang
					$deb[] 	= $model->notabayar->nomor_rekening;
					$kre[] 	= $this->table['piutang_pokok'][$kredit->jenis_pinjaman];
					$ttl[] 	= abs($angs_1['piut_p']);

					$jumlah 	= $jumlah - abs($angs_1['piut_p']);
				}
				elseif($jumlah >= ($hutang - $piut_p - $piut_b)){
					//kalau pelunasan
					$deb[] 	= $model->notabayar->nomor_rekening;
					$kre[] 	= $this->table['pokok'][$kredit->jenis_pinjaman];
					$ttl[] 	= abs($hutang - $piut_p - $piut_b);
					
					$jumlah	= $jumlah - abs($hutang - $piut_p - $piut_b);
				}else{
					//bayar angsuran
					$deb[] 		= $model->notabayar->nomor_rekening;
					$kre[] 		= $this->table['titipan'][$kredit->jenis_pinjaman];
					$ttl[] 		= $jumlah;
					$titipan 	= 0;
				}
			}

			$i 		= $i+1;
		}
		
		return ['kre' => $kre, 'deb' => $deb, 'jumlah' => $ttl];
	}

	public function bayar_bunga(DetailTransaksi $model, Aktif $kredit){
		$jumlah		= abs($this->formatMoneyFrom($model->jumlah));
		$tanggal 	= Carbon::createFromFormat('d/m/Y H:i', $model->notabayar->tanggal)->adddays(1);
		$titipan 	= Calculator::titipanBefore($model->morph_reference_id, $tanggal);

		$i 			= 0;
		while ($jumlah > 0) {
			if($titipan > 0){
				//kalau ada titipan
				$angs_1 	= JadwalAngsuran::where('nomor_kredit', $model->morph_reference_id)
				->orderby('nth', 'asc')
				->wherenull('nomor_faktur')
				->where('tanggal', '<', $tanggal->format('Y-m-d H:i:s'))
				->selectraw('pokok as piut_p')
				->selectraw('bunga as piut_b')
				->selectraw('jumlah as piut_t')
				->skip($i)
				->take(1)
				->first();

				if($titipan >= $angs_1['piut_b'] && $angs_1['piut_t'] > 0){
					//kalau ada angs JT
					$deb[] 	= $this->table['titipan'][$kredit->jenis_pinjaman];
					$kre[] 	= $this->table['piutang_bunga'][$kredit->jenis_pinjaman];
					$ttl[] 	= abs($angs_1['piut_b']);

					$titipan 	= $titipan - $angs_1['piut_b'];
				}elseif($angs_1['piut_b'] > 0){
					//get them balance
					$deb[] 		= $model->notabayar->nomor_rekening;
					$kre[] 		= $this->table['titipan'][$kredit->jenis_pinjaman];
					$ttl[] 		= abs($angs_1['piut_b'] - $titipan);
					
					//bayar angsuran
					$deb[] 	= $this->table['titipan'][$kredit->jenis_pinjaman];
					$kre[] 	= $this->table['piutang_bunga'][$kredit->jenis_pinjaman];
					$ttl[] 	= abs($angs_1['piut_b']);

					$jumlah 	= $jumlah - (abs($angs_1['piut_b'] - $titipan));
					$titipan 	= 0;
				}else{
					//bayar angsuran
					$deb[] 		= $model->notabayar->nomor_rekening;
					$kre[] 		= $this->table['titipan'][$kredit->jenis_pinjaman];
					$ttl[] 		= $jumlah;
					$titipan 	= 0;
				}
			}else{
				//kalau tidak ada titipan
				$angs_1 	= JadwalAngsuran::where('nomor_kredit', $model->morph_reference_id)
				->orderby('nth', 'asc')
				->wherenull('nomor_faktur')
				->where('tanggal', '<', $tanggal->format('Y-m-d H:i:s'))
				->selectraw('pokok as piut_p')
				->selectraw('bunga as piut_b')
				->selectraw('jumlah as piut_t')
				->skip($i)
				->take(1)
				->first();
			
				$hutang 	= Calculator::hutangBefore($model->morph_reference_id, $tanggal);

				if($angs_1['piut_b'] > 0){
					//kalau ada piutang
					$deb[] 	= $model->notabayar->nomor_rekening;
					$kre[] 	= $this->table['piutang_bunga'][$kredit->jenis_pinjaman];
					$ttl[] 	= abs($angs_1['piut_b']);

					$jumlah 	= $jumlah - abs($angs_1['piut_b']);
				}
				elseif($hutang <= 0){
					//kalau pelunasan
					$deb[] 	= $model->notabayar->nomor_rekening;
					$kre[] 	= $this->table['bunga'][$kredit->jenis_pinjaman];
					$ttl[] 	= $jumlah;
					
					$jumlah	= 0;
				}else{
					//bayar angsuran
					$deb[] 		= $model->notabayar->nomor_rekening;
					$kre[] 		= $this->table['titipan'][$kredit->jenis_pinjaman];
					$ttl[] 		= $jumlah;
				}
			}
			$i 		= $i+1;
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