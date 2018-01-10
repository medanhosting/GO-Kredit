<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Service\Traits\IDRTrait;

use Carbon\Carbon, Exception;

class BayarDenda
{
	use IDRTrait;

	public function __construct(Aktif $aktif, $nip_karyawan, $potongan, $tanggal){
		$this->kredit 			= $aktif;
		$this->nip_karyawan 	= $nip_karyawan;
		$this->potongan 		= $potongan;
		$this->tanggal 			= $tanggal;
	}

	public function bayar(){
		$amount 	= AngsuranDetail::whereIn('tag', ['denda', 'potongan_denda'])->where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nota_bayar_id')->sum('amount');

		$first 		= AngsuranDetail::whereIn('tag', ['denda'])->where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nota_bayar_id')->orderby('nth', 'asc')->first();

		//check potongan 
		$potongan 	= $this->formatMoneyFrom($this->potongan);
		if($potongan > 0){
			if($potongan>$amount){
				throw new Exception("Potongan lebih besar dari denda", 1);
			}
			$ptg 	= new AngsuranDetail;
			$ptg->nomor_kredit 	= $this->kredit['nomor_kredit'];
			$ptg->tanggal 		= $this->tanggal;
			$ptg->nth 			= $first->nth;
			$ptg->tag 			= 'potongan_denda';
			$ptg->amount 		= $this->formatMoneyTo(0 - $potongan);
			$ptg->save();
		}

		$denda 	= AngsuranDetail::whereIn('tag', ['denda', 'potongan_denda'])->where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nota_bayar_id')->get();

		$nb 	= new NotaBayar;
		$nb->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
		$nb->nomor_kredit 	= $this->kredit['nomor_kredit'];
		$nb->tanggal 		= $this->tanggal;
		$nb->nip_karyawan 	= $this->nip_karyawan;
		$nb->save();

		foreach ($denda as $k => $v) {
			$v->nota_bayar_id = $nb->id;
			$v->save();
		}
	}
}