<?php

namespace App\Listeners;

use Thunderlabid\Finance\Models\COA;
use Thunderlabid\Finance\Models\TransactionDetail;

class AddCOA
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
		$model 	= $event->data->notabayar;

		foreach ($model->details as $k => $v) {
			$ref 	= $model['nomor_faktur'].'/'.$v['id'];
			$td 	= TransactionDetail::where('morph_reference_tag', 'kredit')->where('morph_reference_id', $ref)->first();

			if(!$td){
				$td = new TransactionDetail;
			}

			switch (strtolower($v['tag'])) {
				case 'realisasi':
					$desc 	= 'Pencairan Kredit #'.$v['nomor_kredit'];
				case 'bunga':
				case 'pokok':
				case 'denda':
					$desc 	= 'Penerimaan Pembayaran '.ucwords(str_replace('_', ' ', $v['tag'])).' Kredit #'.$v['nomor_kredit'];
					break;
				default:
					$desc 	= ucwords(str_replace('_', ' ', $v['tag'])).' Kredit #'.$v['nomor_kredit'];
					break;
			}

			$td->tanggal 	= $model->tanggal;
			$td->amount 	= $model->jumlah;
			$td->morph_reference_tag 	= 'kredit';
			$td->morph_reference_id 	= $ref;
			$td->description 			= $desc;
			$td->save();

			$coa 	= COA::where('transaction_detail_id', $td->id)->where('kode_akun', $model['kode_akun'])->first();
			if(!$coa){
				$coa 	= new COA;
			}
			$coa->transaction_detail_id 	= $td->id;
			$coa->kode_akun 				= $model->kode_akun;
			$coa->save();
		}
	}
}