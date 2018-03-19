<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\SMSGway\Models\SMSQueue;
use Thunderlabid\Pengajuan\Models\Pengajuan;

class QueueingSMSGateway
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
		$model 	= $event->data;

		if($model->getDirty() && str_is($model->tag, 'completed') && !is_null($model->nomor_faktur)){
			//kirim sms
			$data 	= SMSQueue::where('morph_reference_id', $model->nomor_faktur)->where('morph_reference_tag', 'finance.faktur')->first();

			if(!$data){
				$pengajuan	= Pengajuan::findorfail($model->kredit->nomor_pengajuan);

				$isi 		= 'Pembayaran angsuran dengan nomor kredit '.$model->nomor_kredit.' sebesar '.$model->jumlah.' telah dterima. Segera tukarkan kuitansi ke koperasi kami.';
				$telepon 	= $pengajuan->nasabah['telepon'];
				$prefix		= '0';

				if (substr($telepon, 0, strlen($prefix)) == $prefix) {
				    $telepon 	=  preg_replace('/^' . preg_quote($prefix, '/') . '/', '62', $telepon);
				} 

				$new_sms 	= new SMSQueue;
				$new_sms->morph_reference_id	= $model->nomor_faktur;
				$new_sms->morph_reference_tag 	= 'finance.faktur';
				$new_sms->penerima 	= ['telepon' => $telepon];
				$new_sms->isi 		= $isi;
				$new_sms->status	= 'pending';
				$new_sms->save();
			} 	
		}
	}
}