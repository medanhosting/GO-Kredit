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

class JurnalPengakuanPendapatan
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
		// $this->table 	= $this->get_akun_table();
	}

	/**
	 * Handle event
	 * @param  MODEL PUTUSAN $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model 			= $event->data;

		$this->table 	= $this->get_akun_table($model->detail->notabayar);

		$kredit = Aktif::where('nomor_kredit', $model->morph_reference_id)->first();

		$jumlah = $this->formatMoneyFrom($model->jumlah);

		//bukti pencairan
		if(in_array($model->coa->nomor_perkiraan, $this->table['piutang_bunga']) && 
			$jumlah < 0){
			$this->pyd_bunga($model, $kredit);
		}elseif(in_array($model->coa->nomor_perkiraan, $this->table['piutang_denda']) && 
			$jumlah < 0 && str_is($model->detail->notabayar->jenis, 'denda')){
			$this->pyd_denda($model, $kredit);
		}
	}

	private function pyd_bunga($model, $kredit){
		$deb 	= $this->table['pyd_bunga'][$kredit->jenis_pinjaman];
		$kre 	= $this->table['bunga'][$kredit->jenis_pinjaman];

		return $this->jurnal($model, $deb, $kre, $kredit);
	}

	private function pyd_denda($model, $kredit){
		$deb 	= $this->table['pyd_denda'][$kredit->jenis_pinjaman];
		$kre 	= $this->table['denda'][$kredit->jenis_pinjaman];

		return $this->jurnal($model, $deb, $kre, $kredit);
	}

	private function jurnal($model, $deb, $kre, $kredit){
		
		$jumlah		= $this->formatMoneyFrom($model->jumlah);
		
		//tambah debit
		$coa_deb 	= COA::where('nomor_perkiraan', $deb)->where('kode_kantor', $kredit->kode_kantor)->first();

		$data 		= Jurnal::where('detail_transaksi_id', $model->detail_transaksi_id)->where('coa_id', $coa_deb->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->detail_transaksi_id;
		$data->morph_reference_id 	= $model->morph_reference_id;
		$data->morph_reference_tag 	= $model->morph_reference_tag;
		$data->tanggal 				= $model->tanggal;
		$data->coa_id 				= $coa_deb->id;
		$data->jumlah 				= $this->formatMoneyTo(abs($jumlah));
		$data->save();
		
		//kurang kredit
		$coa_kre 	= COA::where('nomor_perkiraan', $kre)->where('kode_kantor', $kredit->kode_kantor)->first();
		$data 		= Jurnal::where('detail_transaksi_id', $model->detail_transaksi_id)->where('coa_id', $coa_kre->id)->first();
		if(!$data){
			$data 	= new Jurnal;
		}
		$data->detail_transaksi_id 	= $model->detail_transaksi_id;
		$data->morph_reference_id 	= $model->morph_reference_id;
		$data->morph_reference_tag 	= $model->morph_reference_tag;
		$data->tanggal 				= $model->tanggal;
		$data->coa_id 				= $coa_kre->id;
		$data->jumlah 				= $this->formatMoneyTo(0 - abs($jumlah));
		$data->save();

		return true;
	}

}