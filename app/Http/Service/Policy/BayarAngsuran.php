<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Service\Traits\IDRTrait;

use Carbon\Carbon;

class BayarAngsuran
{
	use IDRTrait;

	public function __construct(Aktif $aktif, $nip_karyawan, $nth, $tanggal, $rekening_id = null){
		$this->kredit 			= $aktif;
		$this->nip_karyawan 	= $nip_karyawan;
		$this->nth 				= $nth;
		$this->tanggal 			= $tanggal;
		$this->rekening_id 		= $rekening_id;
	}

	public function bayar(){
		///check titipan
		$titipan 	= NotaBayar::wheredoesnthave('details',  function($q){$q;})->where('nomor_kredit', $this->kredit['nomor_kredit'])->sum('jumlah');
		//keluarkan titipan
		if($titipan>0){
			$nbt 	= new NotaBayar;
			$nbt->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
			$nbt->nomor_kredit 	= $this->kredit['nomor_kredit'];
			$nbt->tanggal 		= $this->tanggal;
			$nbt->nip_karyawan 	= $this->nip_karyawan;
			$nbt->jumlah 		= $this->formatMoneyTo($titipan);
			$nbt->rekening_id 	= $this->rekening_id;
			$nbt->save();
		}

		$angsuran 	= AngsuranDetail::whereIn('nth', $this->nth)->where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nota_bayar_id')->get();

		$latest_pay = AngsuranDetail::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenotnull('nota_bayar_id')->wherein('tag', ['bunga', 'pokok'])->orderby('nth', 'desc')->first();
		$should_pay = AngsuranDetail::displaying()->where('nomor_kredit', $this->kredit['nomor_kredit'])->whereIn('nth', $this->nth)->get();

		if($latest_pay){
			$total 	= $this->kredit['jangka_waktu'] - $latest_pay['nth'];
		}else{
			$total 	= $this->kredit['jangka_waktu'];
		}

		$potongan 		= false;

		if(count($should_pay) == $total){
			$potongan 	= PelunasanAngsuran::potongan($this->kredit['nomor_kredit']);
		}

		if($angsuran){
			$total_pay 	= array_sum(array_column($should_pay->toarray(), 'subtotal'));
			$nb 		= new NotaBayar;
			$nb->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
			$nb->nomor_kredit 	= $this->kredit['nomor_kredit'];
			$nb->tanggal 		= $this->tanggal;
			$nb->nip_karyawan 	= $this->nip_karyawan;
			$nb->jumlah 		= $this->formatMoneyTo($total_pay);
			$nb->rekening_id 	= $this->rekening_id;
			$nb->save();

			foreach ($angsuran as $k => $v) {
				if(is_null($v->nota_bayar_id)){
					$v->nota_bayar_id 	= $nb->id;
					$v->save();
				}
			}

			if($potongan){
				$pad 	= new AngsuranDetail;
				$pad->nota_bayar_id	= $nb->id;
				$pad->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$pad->tanggal 		= $this->tanggal;
				$pad->nth 			= ($latest_pay['nth'] * 1) + 1;
				$pad->tag 			= 'potongan';
				$pad->amount 		= $potongan;
				$pad->description 	= 'Potongan Pelunasan';
				$pad->save();
			}
		}
	}
}