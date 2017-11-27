<?php

namespace Thunderlabid\Kredit\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Kredit\Models\Angsuran;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Http\Service\Policy\PerhitunganBunga;

use Carbon\Carbon;

class BuatJadwalAngsuran
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
	 * @param  PenagihanCreated $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model 	= $event->data;

		$pb 	= new PerhitunganBunga($model->plafon_pinjaman, 'Rp 0', $model->suku_bunga, $model->provisi, $model->administrasi, $model->legal, $model->jangka_waktu);

		if(str_is($model->jenis_pinjaman, 'pt')){
			$pb 	= $pb->pt();
		}else{
			$pb 	= $pb->pa();
		}

		$all_angs 	= Angsuran::where('nomor_kredit', $model->nomor_kredit)->delete();
		
		foreach ($pb['angsuran'] as $k => $v) {
			$angsuran 				= new Angsuran;
			$angsuran->kode_kantor 	= $model->kode_kantor;
			$angsuran->nomor_kredit = $model->nomor_kredit;
			$angsuran->issued_at 	= Carbon::createFromFormat('d/m/Y H:i', $model->tanggal)->addmonths($k)->format('d/m/Y H:i');
			$angsuran->save();

			$a_detail 	= new AngsuranDetail;
			$a_detail->angsuran_id 	= $angsuran->id;
			$a_detail->tag 			= 'pokok';
			$a_detail->amount 		= $v['angsuran_pokok'];
			$a_detail->description 	= 'Angsuran Pokok Bulan Ke - '.$k;
			$a_detail->save();

			$a_detail 	= new AngsuranDetail;
			$a_detail->angsuran_id 	= $angsuran->id;
			$a_detail->tag 			= 'bunga';
			$a_detail->amount 		= $v['angsuran_bunga'];
			$a_detail->description 	= 'Angsuran Bunga Bulan Ke - '.$k;
			$a_detail->save();
		}
	}
}