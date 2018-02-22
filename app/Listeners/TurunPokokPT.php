<?php

namespace App\Listeners;

use Thunderlabid\Kredit\Models\Aktif;

use Thunderlabid\Kredit\Models\JadwalAngsuran;

use App\Service\Traits\IDRTrait;

use Carbon\Carbon;
use App\Service\System\Calculator;

class TurunPokokPT
{
	use IDRTrait;

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
		$model 		= $event->data;

		//cek turun pokok
		$kredit 	= Aktif::where('nomor_kredit', $model->morph_reference_id)->first();
		
		if(str_is($kredit['jenis_pinjaman'], 'pt') && str_is($model->tag, 'pokok')){

			$n_dat_day 	= Carbon::createFromFormat('d/m/Y H:i', $model->notabayar->tanggal)->adddays(1);
			$dat_day 	= Carbon::createFromFormat('d/m/Y H:i', $model->notabayar->tanggal);

			//jika pokok pt yang dibayar
			$piut_p 	= Calculator::piutangPokokBefore($model->morph_reference_id, $n_dat_day);
			$sisa_h 	= Calculator::hutangBefore($model->morph_reference_id, $dat_day);

			$bayar_p 	= $this->formatMoneyFrom($model->jumlah);

			if($piut_p <= 0 && ($sisa_h - $bayar_p) > 0){
				//jika tidak ada piutang dan masih ada sisa hutang

				//update schedule bayar berikut
				$jadwal_b 	= JadwalAngsuran::where('nomor_kredit', $model->morph_reference_id)->where('tanggal', '>=', $dat_day->format('Y-m-d H:i:s'))->orderby('nth', 'asc')->get();

				$piut_b 	= Calculator::piutangBungaBefore($model->morph_reference_id, $n_dat_day);
				$sisa_p  	= $sisa_h - $piut_b - $bayar_p;

				$bunga 		= ($sisa_p * $kredit['suku_bunga'])/100;
				
				$start_nth 	= 1;
				foreach ($jadwal_b as $k => $v) {
					if($k==0){
						$start_nth 	= $v->nth;
					}

					$p_d 	= new JadwalAngsuran;
					$p_d->nomor_kredit 	= $model->morph_reference_id;
					$p_d->tanggal 		= $v->tanggal;
					$p_d->nth 			= $v->nth;
					$p_d->deskripsi 	= 'Angsuran Bulan Ke - '.$v->nth;

					if($v->nth!=6){
						$p_d->bunga 	= $this->formatMoneyTo($bunga);
						$p_d->pokok 	= $this->formatMoneyTo(0);
						$p_d->jumlah 	= $this->formatMoneyTo($bunga);
						$p_d->save();
					}else{
						$p_d->bunga 	= $this->formatMoneyTo($bunga);
						$p_d->pokok 	= $this->formatMoneyTo($sisa_p);
						$p_d->jumlah 	= $this->formatMoneyTo($bunga + $sisa_p);
						$p_d->save();
					}

					$p_d->save();
					$v->delete();
				}

				//update schedule bayar sebelumnya
				$jadwal_s 	= JadwalAngsuran::where('nomor_kredit', $model->morph_reference_id)->wherenotnull('nomor_faktur')->where('nth', '<', $start_nth)->orderby('nth', 'desc')->first();

				if($jadwal_s){
					$p_d 	= new JadwalAngsuran;
					$p_d->nomor_kredit 	= $model->morph_reference_id;
					$p_d->nomor_faktur 	= $jadwal_s->nomor_faktur;
					$p_d->tanggal_bayar = $jadwal_s->tanggal_bayar;
					$p_d->tanggal 		= $jadwal_s->tanggal;
					$p_d->nth 			= $jadwal_s->nth;
					$p_d->deskripsi 	= 'Angsuran Bulan Ke - '.$jadwal_s->nth;
					$p_d->bunga 		= $jadwal_s->bunga;
					$p_d->pokok 		= $model->jumlah;
					$p_d->jumlah 		= $this->formatMoneyTo($this->formatMoneyFrom($p_d->bunga) + $this->formatMoneyFrom($model->jumlah));
					$p_d->save();

					$jadwal_s->delete();
				}
			}
		}
	}
}