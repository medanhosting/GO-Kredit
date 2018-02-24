<?php

namespace App\Listeners;

use Thunderlabid\Kredit\Models\Aktif;

use Thunderlabid\Finance\Models\COA;
use Thunderlabid\Finance\Models\Jurnal;

use App\Service\Traits\IDRTrait;

use App\Service\System\Calculator;

use App\Service\System\AkunKreditPA;
use App\Service\System\AkunKreditPT;

class AutoJurnal
{
	use IDRTrait;

	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Handle event
	 * @param  MODEL PUTUSAN $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model 			= $event->data;

		$kredit 		= Aktif::where('nomor_kredit', $model->morph_reference_id)->first();

		//bedakan pa dengan pt
		if(str_is($kredit->jenis_pinjaman, 'pa')){
			$calc 	= new AkunKreditPA();
		}elseif(str_is($kredit->jenis_pinjaman, 'pt')){
			$calc 	= new AkunKreditPT();
		}

		if(str_is($model->tag, 'pencairan')){
			//pencairan kredit
			$akun 	= $calc->pencairan($model, $kredit);
		}elseif(in_array($model->tag, ['provisi', 'administrasi', 'legal', 'biaya_notaris'])){
			//setoran pencairan kredit
			$akun 	= $calc->setoran_pencairan($model, $kredit);
		}
		
		elseif(in_array($model->tag, ['piutang_pokok'])){
			//menimbukan piutang pokok
			$akun 	= $calc->piutang_pokok($model, $kredit);
		}elseif(in_array($model->tag, ['piutang_bunga'])){
			//menimbukan piutang bunga
			$akun 	= $calc->piutang_bunga($model, $kredit);
		}elseif(in_array($model->tag, ['piutang_denda'])){
			//menimbukan piutang denda
			$akun 	= $calc->piutang_denda($model, $kredit);
		}
		
		elseif(in_array($model->tag, ['titipan'])){
			//menimbukan pembayaran titipan
			$akun 	= $calc->bayar_titipan($model, $kredit);
		}elseif(in_array($model->tag, ['pokok'])){
			//menimbukan pembayaran pokok
			$akun 	= $calc->bayar_pokok($model, $kredit);
		}elseif(in_array($model->tag, ['bunga'])){
			//menimbukan pembayaran bunga
			$akun 	= $calc->bayar_bunga($model, $kredit);
		}elseif(in_array($model->tag, ['denda', 'restitusi_denda'])){
			//menimbukan pembayaran denda
			$akun 	= $calc->bayar_denda($model, $kredit);
		}

		$this->jurnal($model, $kredit, $akun['deb'], $akun['kre'], $akun['jumlah']);
	}

	private function jurnal($model, $kredit, $deb, $kre, $jumlah){
		foreach ($jumlah as $k => $v) {
			if($v > 0){
				//tambah debit
				$coa_deb 	= COA::where('nomor_perkiraan', $deb[$k])->where('kode_kantor', $kredit->kode_kantor)->first();
				
				$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_deb->id)->first();
				if(!$data){
					$data 	= new Jurnal;
				}
				$data->detail_transaksi_id 	= $model->id;
				$data->morph_reference_id 	= $model->morph_reference_id;
				$data->morph_reference_tag 	= $model->morph_reference_tag;
				$data->tanggal 				= $model->notabayar->tanggal;
				$data->coa_id 				= $coa_deb->id;
				$data->jumlah 				= $this->formatMoneyTo(abs($v));
				$data->save();
				
				//kurang kredit
				$coa_kre 	= COA::where('nomor_perkiraan', $kre[$k])->where('kode_kantor', $kredit->kode_kantor)->first();
				$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_kre->id)->first();
				if(!$data){
					$data 	= new Jurnal;
				}
				
				$data->detail_transaksi_id 	= $model->id;
				$data->morph_reference_id 	= $model->morph_reference_id;
				$data->morph_reference_tag 	= $model->morph_reference_tag;
				$data->tanggal 				= $model->notabayar->tanggal;
				$data->coa_id 				= $coa_kre->id;
				$data->jumlah 				= $this->formatMoneyTo(0 - abs($v));
				$data->save();
			}
		}

		return true;
	}

}