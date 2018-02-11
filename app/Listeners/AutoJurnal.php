<?php

namespace App\Listeners;

use Thunderlabid\Kredit\Models\Aktif;

use Thunderlabid\Finance\Models\COA;
use Thunderlabid\Finance\Models\Jurnal;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\WaktuTrait;
use App\Service\Traits\JurnalTrait;

use Carbon\Carbon;
use App\Service\System\Calculator;

class AutoJurnal
{
	use IDRTrait;
	use WaktuTrait;
	use JurnalTrait;

	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->table 	= $this->get_akun_table();
	}

	/**
	 * Handle event
	 * @param  MODEL PUTUSAN $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model 	= $event->data;

		$kredit = Aktif::where('nomor_kredit', $model->morph_reference_id)->first();

		//bukti pencairan
		if(str_is($model->tag, 'pencairan')){
			$this->pencairan_kredit($model, $kredit);
		}
		//bukti setoran pencairan
		elseif(in_array($model->tag, ['provisi', 'administrasi', 'legal', 'biaya_notaris'])){
			$this->setoran_pencairan($model, $kredit);
		}
		//memorial jurnal pagi
		elseif(str_is($model->notabayar->jenis, 'memorial') && in_array($model->tag, ['pokok', 'bunga', 'denda'])){
			$this->memorial_jurnal_pagi($model, $kredit);
		}
		//angsuran sementara or memorial titipan
		elseif(in_array($model->tag, ['titipan'])){
			$this->angsuran_sementara($model, $kredit);
		}
		//angsuran
		elseif(str_is($model->notabayar->jenis, 'angsuran') && in_array($model->tag, ['pokok', 'bunga'])){
			$this->angsuran($model, $kredit);
		}
		//denda
		elseif(str_is($model->notabayar->jenis, 'denda') || str_is($model->notabayar->jenis, 'restitusi_denda')){
			$this->denda($model, $kredit);
		}
	}

	private function pencairan_kredit($model, $kredit){
		$kre 	= $model->notabayar->nomor_rekening;
		$deb 	= $this->table['pokok'][$kredit->jenis_pinjaman];

		return $this->jurnal($model, $deb, $kre, $kredit);
	}

	private function setoran_pencairan($model, $kredit){
		$deb 	= $model->notabayar->nomor_rekening;
		$kre 	= $this->table[$model->tag][$kredit->jenis_pinjaman];

		return $this->jurnal($model, $deb, $kre, $kredit);
	}

	private function memorial_jurnal_pagi($model, $kredit){
		//2. check mode
		if(in_array($model->tag, ['pokok', 'bunga'])){
			return $this->angsuran($model, $kredit);
		}else{
			//denda
			$deb 	= $this->table['piutang_denda'][$kredit->jenis_pinjaman];
			$kre 	= $this->table['denda'][$kredit->jenis_pinjaman];
		}

		return $this->jurnal($model, $deb, $kre, $kredit);
	}

	private function angsuran_sementara($model, $kredit){
		$deb 	= $this->table['kas_titipan'][$kredit->jenis_pinjaman];
		$kre 	= $this->table['titipan'][$kredit->jenis_pinjaman];

		return $this->jurnal($model, $deb, $kre, $kredit);
	}

	private function angsuran($model, $kredit){
		//2. check mode
		$titipan 		= Calculator::titipanBefore($model->morph_reference_id, Carbon::createFromFormat('d/m/Y H:i', $model->notabayar->tanggal)->adddays(1));

		if($titipan > 0){
			// 2a. Jika ada titipan
			$deb 		= $this->table['titipan'][$kredit->jenis_pinjaman];

			if(str_is($model->tag, 'pokok')){
				//2ai. pokok
				$piut_p 	= Calculator::piutangPokokBefore($model->morph_reference_id, Carbon::createFromFormat('d/m/Y H:i', $model->notabayar->tanggal)->adddays(1));
				if($piut_p > 0){
					$kre 	= $this->table['piutang_pokok'][$kredit->jenis_pinjaman];
				}else{
					$kre 	= $this->table['pokok'][$kredit->jenis_pinjaman];
				}
			}else{
				//2aii. bunga
				$piut_b 	= Calculator::piutangBungaBefore($model->morph_reference_id, Carbon::createFromFormat('d/m/Y H:i', $model->notabayar->tanggal)->adddays(1));
				if($piut_b > 0){
					$kre 	= $this->table['piutang_bunga'][$kredit->jenis_pinjaman];
				}else{
					$kre 	= $this->table['bunga'][$kredit->jenis_pinjaman];
				}
			}
		}else{
			if(str_is($model->tag, 'pokok')){
				//2ai. pokok
				if(str_is($model->notabayar->jenis, 'memorial')){
					$deb 	= $this->table['piutang_pokok'][$kredit->jenis_pinjaman];
					$kre 	= $this->table['pokok'][$kredit->jenis_pinjaman];
				}else{
					$deb 	= $model->notabayar->nomor_rekening;
					$piut_p 	= Calculator::piutangPokokBefore($model->morph_reference_id, Carbon::createFromFormat('d/m/Y H:i', $model->notabayar->tanggal)->adddays(1));
					if($piut_p > 0){
						$kre 	= $this->table['piutang_pokok'][$kredit->jenis_pinjaman];
					}else{
						$kre 	= $this->table['pokok'][$kredit->jenis_pinjaman];
					}
				}
			}else{
				if(str_is($model->notabayar->jenis, 'memorial')){
					$deb 	= $this->table['piutang_bunga'][$kredit->jenis_pinjaman];
					$kre 	= $this->table['bunga'][$kredit->jenis_pinjaman];
				}else{
					$deb 	= $model->notabayar->nomor_rekening;
					//2aii. bunga
					$piut_b 	= Calculator::piutangBungaBefore($model->morph_reference_id, Carbon::createFromFormat('d/m/Y H:i', $model->notabayar->tanggal)->adddays(1));
					if($piut_b > 0){
						$kre 	= $this->table['piutang_bunga'][$kredit->jenis_pinjaman];
					}else{
						$kre 	= $this->table['bunga'][$kredit->jenis_pinjaman];
					}
				}
			}
		}

		return $this->jurnal($model, $deb, $kre, $kredit);
	}

	private function denda($model, $kredit){
		if(str_is($model->tag, 'denda')){
			$deb 	= $model->notabayar->nomor_rekening;
		}else{
			$deb 	= $this->table['denda_hapus_buku'][$kredit->jenis_pinjaman];
		}

		$kre 		= $this->table['piutang_denda'][$kredit->jenis_pinjaman];

		return $this->jurnal($model, $deb, $kre, $kredit);
	}

	private function jurnal($model, $deb, $kre, $kredit){
		
		$jumlah		= $this->formatMoneyFrom($model->jumlah);
		
		//tambah debit
		$coa_deb 	= COA::where('nomor_perkiraan', $deb)->where('kode_kantor', $kredit->kode_kantor)->first();

		$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_deb->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->id;
		$data->morph_reference_id 	= $model->morph_reference_id;
		$data->morph_reference_tag 	= $model->morph_reference_tag;
		$data->tanggal 				= $model->notabayar->tanggal;
		$data->coa_id 				= $coa_deb->id;
		$data->jumlah 				= $this->formatMoneyTo(abs($jumlah));
		$data->save();
		
		//kurang kredit
		$coa_kre 	= COA::where('nomor_perkiraan', $kre)->where('kode_kantor', $kredit->kode_kantor)->first();
		$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_kre->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->id;
		$data->morph_reference_id 	= $model->morph_reference_id;
		$data->morph_reference_tag 	= $model->morph_reference_tag;
		$data->tanggal 				= $model->notabayar->tanggal;
		$data->coa_id 				= $coa_kre->id;
		$data->jumlah 				= $this->formatMoneyTo(0 - abs($jumlah));
		$data->save();

		return true;
	}

}