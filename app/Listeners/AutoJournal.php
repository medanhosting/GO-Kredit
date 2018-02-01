<?php

namespace App\Listeners;

use Thunderlabid\Kredit\Models\Aktif;

use Thunderlabid\Finance\Models\COA;
use Thunderlabid\Finance\Models\Jurnal;

use App\Service\Traits\IDRTrait;

class AutoJournal
{
	use IDRTrait;

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
		$model 	= $event->data;

		//untuk realisasi; kas pada piutang
		if(str_is($model->tag, 'pencairan')){
			$this->journal_pencairan($model);
		}
		elseif(in_array($model->tag, ['provisi', 'administrasi', 'legal', 'biaya_notaris'])){
			$this->journal_setoran_pencairan($model);
		}
	}

	private function journal_pencairan($model){
		//check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->notabayar->morph_reference_id)->first();
		$coa_kas 	= COA::where('nomor_perkiraan', '100.100')->where('kode_kantor', $kredit->kode_kantor)->first();

		if(str_is($kredit->jenis_pinjaman, 'pa')){
			$coa_piut 	= COA::where('nomor_perkiraan', '120.100')->where('kode_kantor', $kredit->kode_kantor)->first();
		}else{
			$coa_piut 	= COA::where('nomor_perkiraan', '120.200')->where('kode_kantor', $kredit->kode_kantor)->first();
		}

		$jumlah 		= $this->formatMoneyFrom($model->jumlah);

		//tambah piutang
		$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_piut->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->id;
		$data->tanggal 				= $model->notabayar->tanggal;
		$data->coa_id 				= $coa_piut->id;
		$data->jumlah 				= $this->formatMoneyTo(abs($jumlah));
		$data->save();
		
		//kurang kas
		$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_kas->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->id;
		$data->tanggal 				= $model->notabayar->tanggal;
		$data->coa_id 				= $coa_kas->id;
		$data->jumlah 				= $this->formatMoneyTo(0 - abs($jumlah));
		$data->save();
	}

	private function journal_setoran_pencairan($model){
		//check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->notabayar->morph_reference_id)->first();
		$coa_kas 	= COA::where('nomor_perkiraan', '100.100')->where('kode_kantor', $kredit->kode_kantor)->first();

		if(str_is($model->tag, 'provisi')){
			$coa_pdpt 	= COA::where('nomor_perkiraan', '401.201')->where('kode_kantor', $kredit->kode_kantor)->first();
		}elseif(str_is($model->tag, 'administrasi')){
			$coa_pdpt 	= COA::where('nomor_perkiraan', '401.202')->where('kode_kantor', $kredit->kode_kantor)->first();
		}elseif(str_is($model->tag, 'legal')){
			$coa_pdpt 	= COA::where('nomor_perkiraan', '401.204')->where('kode_kantor', $kredit->kode_kantor)->first();
		}else{
			$coa_pdpt 	= COA::where('nomor_perkiraan', '401.205')->where('kode_kantor', $kredit->kode_kantor)->first();
		}

		$jumlah 		= $this->formatMoneyFrom($model->jumlah);

		//tambah kas
		$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_kas->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->id;
		$data->tanggal 				= $model->notabayar->tanggal;
		$data->coa_id 				= $coa_kas->id;
		$data->jumlah 				= $this->formatMoneyTo(abs($jumlah));
		$data->save();
		
		//kurang pendapatan
		$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_pdpt->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->id;
		$data->tanggal 				= $model->notabayar->tanggal;
		$data->coa_id 				= $coa_pdpt->id;
		$data->jumlah 				= $this->formatMoneyTo(0 - abs($jumlah));
		$data->save();
	}
}