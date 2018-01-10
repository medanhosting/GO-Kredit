<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\SuratPeringatan;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\WaktuTrait;

use Carbon\Carbon;

class FeedBackPenagihan
{
	use IDRTrait;
	use WaktuTrait;

	public function __construct(Aktif $aktif, $nip_karyawan, $tanggal, $penerima, $nominal = null, $rekening_id = null){
		$this->kredit 			= $aktif;
		$this->nip_karyawan 	= $nip_karyawan;
		$this->tanggal 			= $tanggal;
		$this->penerima 		= $penerima;
		$this->nominal 			= $this->formatMoneyFrom($nominal);
		$this->rekening_id 		= $rekening_id;
	}

	public function bayar(){
		$find_exists_sp 		= SuratPeringatan::where('nomor_kredit', $this->kredit['nomor_kredit'])->where('tanggal', '<=', $this->formatdatetimefrom($this->tanggal))->wheredoesnthave('penagihan', function($q){$q;})->get();

		$tagih 		= new Penagihan;
		$tagih->nip_karyawan 	= $this->nip_karyawan;
		$tagih->tanggal 		= $this->tanggal;
		$tagih->penerima 		= $this->penerima;
		$tagih->nomor_kredit 	= $this->kredit['nomor_kredit'];
		$tagih->tag 			= 'completed';
		$tagih->save();

		foreach ($find_exists_sp as $k => $v) {
			$v->penagihan_id 	= $tagih->id;
			$v->save();
		}

		if(!is_null($this->nominal)){

			//check tunggakan
			$today		= Carbon::now();
			$tunggakan  = AngsuranDetail::displaying()->where('nomor_kredit', $this->kredit['nomor_kredit'])->where('tanggal', '<=', $today->format('Y-m-d H:i:s'))->get();

			$total 		= array_sum(array_column($tunggakan->toArray(), 'subtotal'));

			if($total == $this->nominal){
				//simpan nota bayar
				$nota_b = new NotaBayar;
				$nota_b->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$nota_b->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
				$nota_b->tanggal 		= $this->tanggal;
				$nota_b->nip_karyawan 	= $this->nip_karyawan;
				$nota_b->penagihan_id 	= $tagih->id;
				$nota_b->jumlah 		= $this->formatMoneyTo($this->nominal);
				$nota_b->rekening_id 	= $this->rekening_id;
				$nota_b->save();
	
				$nb 	= AngsuranDetail::whereIn('nth', array_column($tunggakan->toArray(), 'nth'))->update(['nota_bayar_id' => $nota_b->id]);
			}elseif($total < $this->nominal){
				//simpan nota bayar
				$nota_b = new NotaBayar;
				$nota_b->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$nota_b->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
				$nota_b->tanggal 		= $this->tanggal;
				$nota_b->nip_karyawan 	= $this->nip_karyawan;
				$nota_b->penagihan_id 	= $tagih->id;
				$nota_b->rekening_id 	= $this->rekening_id;
				$nota_b->jumlah 		= $this->formatMoneyTo($total);
				$nota_b->save();

				$nb 	= AngsuranDetail::whereIn('nth', array_column($tunggakan->toArray(), 'nth'))->update(['nota_bayar_id' => $nota_b->id]);

				//simpan nota bayar
				$nota_b2 = new NotaBayar;
				$nota_b2->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$nota_b2->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
				$nota_b2->tanggal 		= $this->tanggal;
				$nota_b2->nip_karyawan 	= $this->nip_karyawan;
				$nota_b2->penagihan_id 	= $tagih->id;
				$nota_b2->rekening_id 	= $this->rekening_id;
				$nota_b2->jumlah 		= $this->formatMoneyTo($this->nominal - $total);
				$nota_b2->save();
			}else{
				//simpan nota bayar
				$nota_b = new NotaBayar;
				$nota_b->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$nota_b->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
				$nota_b->tanggal 		= $this->tanggal;
				$nota_b->nip_karyawan 	= $this->nip_karyawan;
				$nota_b->rekening_id 	= $this->rekening_id;
				$nota_b->penagihan_id 	= $tagih->id;
				$nota_b->jumlah 		= $this->formatMoneyTo($this->nominal);
				$nota_b->save();
			}
		}
		return true;
	}
}