<?php

namespace Thunderlabid\Kredit\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Kredit\Models\JadwalAngsuran;

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
		$model 		= $event->data;

		$pb 		= new PerhitunganBunga($model->plafon_pinjaman, 'Rp 0', $model->suku_bunga, $model->provisi, $model->administrasi, $model->legal, $model->jangka_waktu);

		if(str_is($model->jenis_pinjaman, 'pt')){
			$pb 	= $pb->pt();
		}else{
			$pb 	= $pb->pa();
		}

		foreach ($pb['angsuran'] as $k => $v) {
			$p_d 	= new JadwalAngsuran;
			$p_d->nomor_kredit = $model->nomor_kredit;
			$p_d->tanggal 		= Carbon::createFromFormat('d/m/Y H:i', $model->tanggal)->addmonths($k)->format('d/m/Y H:i');
			$p_d->nth 			= $k;
			$p_d->jumlah 		= $v['total_angsuran'];
			$p_d->deskripsi 	= 'Angsuran Bulan Ke - '.$k;
			$p_d->save();
		}
	}
}
