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
			//1. jurnal bunga
			$piut		= abs(Jurnal::whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['140.100', '140.200']);})->where('morph_reference_id', $kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit')->sum('jumlah'));
			if($piut > 0){
				$coa_deb 	= COA::where('nomor_perkiraan', $model->notabayar->nomor_rekening)->where('kode_kantor', $kode_kantor)->first();
				//1.a. jurnal bunga JT
				if(str_is($kredit->jenis_pinjaman, 'pa')){
					//1.a.i. jurnal bunga JT PA
					$coa_kre 	= COA::where('nomor_perkiraan', '140.100')->where('kode_kantor', $kode_kantor)->first();
				}elseif(str_is($kredit->jenis_pinjaman, 'pt')){
					//1.a.ii. jurnal bunga JT PT
					$coa_kre 	= COA::where('nomor_perkiraan', '140.200')->where('kode_kantor', $kode_kantor)->first();
				}
			}else{
				//1.b. jurnal bunga TJT
				//1.b.i jurnal bunga TJT PA
				//1.b.ii jurnal bunga TJT PT
				$coa_deb 	= COA::where('nomor_perkiraan', $model->notabayar->nomor_rekening)->where('kode_kantor', $kode_kantor)->first();
				$coa_kre 	= COA::where('nomor_perkiraan', '260.110')->where('kode_kantor', $kode_kantor)->first();
			}
		}elseif(str_is($model->tag, 'pokok')){
			//1. jurnal pokok
			$piut		= abs(Jurnal::whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['120.300', '120.400']);})->where('morph_reference_id', $kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit')->sum('jumlah'));
				
			if($piut > 0){
				//1.a. jurnal pokok JT
				$coa_deb 	= COA::where('nomor_perkiraan', $model->notabayar->nomor_rekening)->where('kode_kantor', $kode_kantor)->first();

				if(str_is($kredit->jenis_pinjaman, 'pa')){
					//1.a.i. jurnal pokok JT PA
					$coa_kre 	= COA::where('nomor_perkiraan', '120.300')->where('kode_kantor', $kode_kantor)->first();

				}elseif(str_is($kredit->jenis_pinjaman, 'pt')){
					//1.a.ii. jurnal pokok JT PT
					$coa_kre 	= COA::where('nomor_perkiraan', '120.400')->where('kode_kantor', $kode_kantor)->first();
				}
			}else{
				//1.b. jurnal pokok TJT
				$coa_deb 	= COA::where('nomor_perkiraan', $model->notabayar->nomor_rekening)->where('kode_kantor', $kode_kantor)->first();
				
				if(str_is($kredit->jenis_pinjaman, 'pa')){
					//1.b.i. jurnal bunga TJT PA
					$coa_kre 	= COA::where('nomor_perkiraan', '120.100')->where('kode_kantor', $kode_kantor)->first();

				}elseif(str_is($kredit->jenis_pinjaman, 'pt')){
					//1.b.ii. jurnal bunga TJT PT
					$coa_kre 	= COA::where('nomor_perkiraan', '120.200')->where('kode_kantor', $kode_kantor)->first();
				}
			}
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
				//kalau piutang pokok tidak 0 masuk piutang pokok
				$piut		= abs(Jurnal::whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['120.300']);})->where('morph_reference_id', $kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit')->sum('jumlah'));

				if($piut > 0){
					//1.a.i. do restitusi pokok jt PA
					$coa_kre 	= COA::where('nomor_perkiraan', '120.300')->where('kode_kantor', $kode_kantor)->first();
				}else{
					//1.a.ii. do pada bukan JT
					$coa_kre 	= COA::where('nomor_perkiraan', '120.100')->where('kode_kantor', $kode_kantor)->first();
				}

			}
		}
		elseif(str_is($model->tag, 'restitusi_titipan_bunga')){
			$piut		= abs(Jurnal::whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['140.100', '140.200']);})->where('morph_reference_id', $kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit')->sum('jumlah'));

			if($piut > 0){
				//1.a. do restitusi bunga
				if(str_is($kredit->jenis_pinjaman, 'pa')){
					$coa_kre 	= COA::where('nomor_perkiraan', '140.100')->where('kode_kantor', $kode_kantor)->first();
					//1.a.i. do restitusi bunga jt PA
				}elseif(str_is($kredit->jenis_pinjaman, 'pt')){
					//1.a.i. do restitusi bunga jt PA
					$coa_kre 	= COA::where('nomor_perkiraan', '140.200')->where('kode_kantor', $kode_kantor)->first();
				}
			}else{
				$coa_kre 	= COA::where('nomor_perkiraan', '260.110')->where('kode_kantor', $kode_kantor)->first();
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