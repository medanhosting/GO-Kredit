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
		elseif(str_is($model->tag, 'denda', 'restitusi_denda')){
			$this->journal_denda($model);
		}
		elseif(in_array($model->tag, ['titipan'])){
			$this->journal_setoran_angsuran_sementara($model);
		}
		elseif(in_array($model->tag, ['restitusi_titipan_pokok', 'restitusi_titipan_bunga'])){
			$this->journal_restitusi_titipan($model);
		}
	}

	private function journal_pencairan($model){
		//check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->morph_reference_id)->first();
		$kode_kantor 	= $kredit->kode_kantor;

		// $kode_kantor 	= explode('.', $model->notabayar->nomor_faktur);
		// $kode_kantor 	= $kode_kantor[0].'.'.$kode_kantor[1];

		$coa_kre 	= COA::where('nomor_perkiraan', $model->notabayar->nomor_rekening)->where('kode_kantor', $kode_kantor)->first();

		if(str_is($kredit->jenis_pinjaman, 'pa')){
			$coa_deb 	= COA::where('nomor_perkiraan', '120.100')->where('kode_kantor', $kode_kantor)->first();
		}else{
			$coa_deb 	= COA::where('nomor_perkiraan', '120.200')->where('kode_kantor', $kode_kantor)->first();
		}

		$jumlah 		= $this->formatMoneyFrom($model->jumlah);

		return $this->jurnal($model, $coa_deb, $coa_kre, $jumlah);
	}

	private function journal_setoran_pencairan($model){
		//check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->morph_reference_id)->first();
		$kode_kantor 	= $kredit->kode_kantor;

		// $kode_kantor 	= explode('.', $model->notabayar->nomor_faktur);
		// $kode_kantor 	= $kode_kantor[0].'.'.$kode_kantor[1];

		$coa_deb 	= COA::where('nomor_perkiraan', $model->notabayar->nomor_rekening)->where('kode_kantor', $kode_kantor)->first();

		if(str_is($model->tag, 'provisi')){
			$coa_kre 	= COA::where('nomor_perkiraan', '401.201')->where('kode_kantor', $kode_kantor)->first();
		}elseif(str_is($model->tag, 'administrasi')){
			$coa_kre 	= COA::where('nomor_perkiraan', '401.202')->where('kode_kantor', $kode_kantor)->first();
		}elseif(str_is($model->tag, 'legal')){
			$coa_kre 	= COA::where('nomor_perkiraan', '401.303')->where('kode_kantor', $kode_kantor)->first();
		}else{
			$coa_kre 	= COA::where('nomor_perkiraan', '200.230')->where('kode_kantor', $kode_kantor)->first();
		}

		$jumlah 		= $this->formatMoneyFrom($model->jumlah);

		return $this->jurnal($model, $coa_deb, $coa_kre, $jumlah);
	}


	private function journal_setoran_angsuran($model){
		//1. check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->morph_reference_id)->first();
		$kode_kantor 	= $kredit->kode_kantor;

		// $kode_kantor 	= explode('.', $model->notabayar->nomor_faktur);
		// $kode_kantor 	= $kode_kantor[0].'.'.$kode_kantor[1];


		if(str_is($model->tag, 'bunga')){
			//1.a. do bunga
			if(str_is($model->notabayar->jenis, 'memorial')){
				//1.a.i. do memorial bunga jt
				if(str_is($kredit->jenis_pinjaman, 'pa')){
					//1.a.i.1. do memorial bunga jt PA
					$coa_deb 	= COA::where('nomor_perkiraan', '140.100')->where('kode_kantor', $kode_kantor)->first();
				}else{
					//1.a.i.2. do memorial bunga jt PT
					$coa_deb 	= COA::where('nomor_perkiraan', '140.200')->where('kode_kantor', $kode_kantor)->first();
				}
				$coa_kre 	= COA::where('nomor_perkiraan', '260.110')->where('kode_kantor', $kode_kantor)->first();
			}
			
			//1.a.ii. do bayar bunga jt
			//1.a.iii. do bayar bunga

		}elseif(str_is($model->tag, 'pokok')){
			//1.a. do pokok
			if(str_is($model->notabayar->jenis, 'memorial')){
				//1.a.i. do memorial pokok jt
				if(str_is($kredit->jenis_pinjaman, 'pa')){
					//1.a.i.1. do memorial pokok jt PA
					$coa_deb 	= COA::where('nomor_perkiraan', '120.300')->where('kode_kantor', $kode_kantor)->first();
					$coa_kre 	= COA::where('nomor_perkiraan', '120.100')->where('kode_kantor', $kode_kantor)->first();
				}else{
					//1.a.i.2. do memorial pokok jt PT
					$coa_deb 	= COA::where('nomor_perkiraan', '120.400')->where('kode_kantor', $kode_kantor)->first();
					$coa_kre 	= COA::where('nomor_perkiraan', '120.200')->where('kode_kantor', $kode_kantor)->first();
				}
			}

			//1.a.ii. do bayar pokok jt
			//1.a.iii. do bayar pokok
		}

		$jumlah 		= $this->formatMoneyFrom($model->jumlah);
		
		return $this->jurnal($model, $coa_deb, $coa_kre, $jumlah);
	}

	private function journal_setoran_angsuran_sementara($model){
		//check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->morph_reference_id)->first();
		$kode_kantor 	= $kredit->kode_kantor;

		// $kode_kantor 	= explode('.', $model->notabayar->nomor_faktur);
		// $kode_kantor 	= $kode_kantor[0].'.'.$kode_kantor[1];
		
		if(str_is($model->tag, 'titipan')){
			//1.a. do titipan
			$coa_deb 	= COA::where('nomor_perkiraan', '100.300')->where('kode_kantor', $kode_kantor)->first();
			$coa_kre 	= COA::where('nomor_perkiraan', '200.210')->where('kode_kantor', $kode_kantor)->first();
		}

		$jumlah 		= $this->formatMoneyFrom($model->jumlah);

		return $this->jurnal($model, $coa_deb, $coa_kre, $jumlah);
	}

	private function journal_restitusi_titipan($model){
		//1. check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->morph_reference_id)->first();
		$kode_kantor= $kredit->kode_kantor;
		
		$coa_deb 	= COA::where('nomor_perkiraan', '200.210')->where('kode_kantor', $kode_kantor)->first();

		if(str_is($model->tag, 'restitusi_titipan_pokok')){
			//1.a. do restitusi pokok
			if(str_is($kredit->jenis_pinjaman, 'pa')){
				//1.a.i. do restitusi pokok jt PA
				$coa_kre 	= COA::where('nomor_perkiraan', '120.300')->where('kode_kantor', $kode_kantor)->first();
			}
		}
		elseif(str_is($model->tag, 'restitusi_titipan_bunga')){
			//1.a. do restitusi pokok
			if(str_is($kredit->jenis_pinjaman, 'pa')){
				//1.a.i. do restitusi pokok jt PA
				$coa_kre 	= COA::where('nomor_perkiraan', '140.100')->where('kode_kantor', $kode_kantor)->first();
			}elseif(str_is($kredit->jenis_pinjaman, 'pt')){
				//1.a.i. do restitusi pokok jt PA
				$coa_kre 	= COA::where('nomor_perkiraan', '140.200')->where('kode_kantor', $kode_kantor)->first();
			}
		}

		$jumlah 	= $this->formatMoneyFrom($model->jumlah);

		return $this->jurnal($model, $coa_deb, $coa_kre, $jumlah);
	}

	private function journal_denda($model){
		//check kredit
		$kredit 	= Aktif::where('nomor_kredit', $model->morph_reference_id)->first();
		$kode_kantor 	= $kredit->kode_kantor;

		// $kode_kantor 	= explode('.', $model->notabayar->nomor_faktur);
		// $kode_kantor 	= $kode_kantor[0].'.'.$kode_kantor[1];
		
		if(str_is($model->tag, 'denda')){
			//1.a. do denda
			if(str_is($model->notabayar->jenis, 'memorial')){
				//1.a.i. do memorial denda
				$coa_deb 	= COA::where('nomor_perkiraan', '140.600')->where('kode_kantor', $kode_kantor)->first();
				$coa_kre 	= COA::where('nomor_perkiraan', '260.120')->where('kode_kantor', $kode_kantor)->first();
			}
			//1.a.ii. do bayar piut denda

		}elseif(str_is($model->tag, 'restitusi_denda')){

		}

		$jumlah 		= $this->formatMoneyFrom($model->jumlah);

		return $this->jurnal($model, $coa_deb, $coa_kre, $jumlah);
	}

	private function jurnal($model, $coa_deb, $coa_kre, $jumlah){
		//tambah debit
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
	}
}