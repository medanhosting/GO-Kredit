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
		elseif(in_array($model->tag, ['pokok', 'bunga'])){
			$this->journal_setoran_angsuran($model);
		}
		elseif(in_array($model->tag, ['titipan_pokok', 'titipan_bunga'])){
			$this->journal_setoran_angsuran_sementara($model);
		}
		elseif(in_array($model->tag, ['restitusi_titipan_pokok', 'restitusi_titipan_bunga'])){
			$this->journal_restitusi_titipan($model);
		}
		elseif(str_is($model->tag, 'denda')){
			$this->journal_denda($model);
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


	private function journal_setoran_angsuran($model){
		//check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->notabayar->morph_reference_id)->first();

		if(str_is($model->tag, 'bunga')){
			if(str_is($kredit->jenis_pinjaman, 'pa')){
				$coa_piut 	= COA::where('nomor_perkiraan', '140.100')->where('kode_kantor', $kredit->kode_kantor)->first();
			}else{
				$coa_piut 	= COA::where('nomor_perkiraan', '140.200')->where('kode_kantor', $kredit->kode_kantor)->first();
			}
			$coa_pdpt 		= COA::where('nomor_perkiraan', '260.110')->where('kode_kantor', $kredit->kode_kantor)->first();
		}elseif(str_is($model->tag, 'pokok')){
			if(str_is($kredit->jenis_pinjaman, 'pa')){
				$coa_piut 	= COA::where('nomor_perkiraan', '120.300')->where('kode_kantor', $kredit->kode_kantor)->first();
				$coa_pdpt 	= COA::where('nomor_perkiraan', '120.100')->where('kode_kantor', $kredit->kode_kantor)->first();
			}else{
				$coa_piut 	= COA::where('nomor_perkiraan', '120.400')->where('kode_kantor', $kredit->kode_kantor)->first();
				$coa_pdpt 	= COA::where('nomor_perkiraan', '120.200')->where('kode_kantor', $kredit->kode_kantor)->first();
			}
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

	private function journal_setoran_angsuran_sementara($model){
		//check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->notabayar->morph_reference_id)->first();
		
		$jumlah 	= $this->formatMoneyFrom($model->jumlah);

		if(str_is($kredit->jenis_pinjaman, 'pa')){
			if(str_is($model->tag, 'titipan_bunga')){
				$coa_piut 	= COA::where('nomor_perkiraan', '140.100')->where('kode_kantor', $kredit->kode_kantor)->first();
			}else{
				$coa_piut 	= COA::where('nomor_perkiraan', '120.300')->where('kode_kantor', $kredit->kode_kantor)->first();
			}
		}else{
			$coa_piut 	= COA::where('nomor_perkiraan', '140.200')->where('kode_kantor', $kredit->kode_kantor)->first();
		}
		$coa_titip 		= COA::where('nomor_perkiraan', '200.210')->where('kode_kantor', $kredit->kode_kantor)->first();

		//tambah titipan
		$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_titip->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->id;
		$data->tanggal 				= $model->notabayar->tanggal;
		$data->coa_id 				= $coa_titip->id;
		$data->jumlah 				= $this->formatMoneyTo(abs($jumlah));
		$data->save();
		
		//kurang piutang
		$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_piut->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->id;
		$data->tanggal 				= $model->notabayar->tanggal;
		$data->coa_id 				= $coa_piut->id;
		$data->jumlah 				= $this->formatMoneyTo(0 - abs($jumlah));
		$data->save();
	}

	private function journal_restitusi_titipan($model){
		//check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->notabayar->morph_reference_id)->first();
		
		$jumlah 	= $this->formatMoneyFrom($model->jumlah);
		
		$coa_ttp 	= COA::where('nomor_perkiraan', '200.210')->where('kode_kantor', $kredit->kode_kantor)->first();

		if(str_is($kredit->jenis_pinjaman, 'pa')){
			if(str_is($model->tag, 'restitusi_titipan_pokok')){
				$coa_piut 	= COA::where('nomor_perkiraan', '120.300')->where('kode_kantor', $kredit->kode_kantor)->first();
			}else{
				$coa_piut 	= COA::where('nomor_perkiraan', '140.100')->where('kode_kantor', $kredit->kode_kantor)->first();
			}
		}else{
			$coa_piut 	= COA::where('nomor_perkiraan', '140.200')->where('kode_kantor', $kredit->kode_kantor)->first();
		}

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
		
		//kurang titipan
		$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_ttp->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->id;
		$data->tanggal 				= $model->notabayar->tanggal;
		$data->coa_id 				= $coa_ttp->id;
		$data->jumlah 				= $this->formatMoneyTo(0 - abs($jumlah));
		$data->save();
	}

	private function journal_denda($model){
		//check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->notabayar->morph_reference_id)->first();
		
		$jumlah 	= $this->formatMoneyFrom($model->jumlah);
		
		$coa_piut 	= COA::where('nomor_perkiraan', '140.600')->where('kode_kantor', $kredit->kode_kantor)->first();
		$coa_pyd 	= COA::where('nomor_perkiraan', '260.120')->where('kode_kantor', $kredit->kode_kantor)->first();

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
		
		//kurang pyd
		$data 		= Jurnal::where('detail_transaksi_id', $model->id)->where('coa_id', $coa_pyd->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->id;
		$data->tanggal 				= $model->notabayar->tanggal;
		$data->coa_id 				= $coa_pyd->id;
		$data->jumlah 				= $this->formatMoneyTo(0 - abs($jumlah));
		$data->save();
	}
}