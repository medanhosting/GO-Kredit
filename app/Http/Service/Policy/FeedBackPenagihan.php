<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Service\Traits\IDRTrait;

use Carbon\Carbon;

class FeedBackPenagihan
{
	use IDRTrait;

	public function __construct(Aktif $aktif, $nip_karyawan, $tanggal, $penerima, $nominal = null){
		$this->kredit 			= $aktif;
		$this->nip_karyawan 	= $nip_karyawan;
		$this->tanggal 			= $tanggal;
		$this->penerima 		= $penerima;
		$this->nominal 			= $this->formatMoneyFrom($nominal);
	}

	public function bayar(){
		$tagih 		= new Penagihan;
		$tagih->nip_karyawan 	= $this->nip_karyawan;
		$tagih->tanggal 		= $this->tanggal;
		$tagih->penerima 		= $this->penerima;
		$tagih->nomor_kredit 	= $this->kredit['nomor_kredit'];
		$tagih->tag 			= 'completed';
		$tagih->save();

		if(!is_null($this->nominal)){

			//check tunggakan
			$today		= Carbon::now();
			$tunggakan  = AngsuranDetail::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherehas('kredit', function($q){$q->where('kode_kantor',$this->aktif['nomor_kredit']);})->HitungTunggakanBeberapaWaktuLalu($today)->orderby('tanggal', 'asc')->get();

			$total 		= array_sum(array_column($tunggakan->toArray(), 'subtotal'));
			if($total == $this->nominal){
				//simpan nota bayar
				$nota_b = new NotaBayar;
				$nota_b->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$nota_b->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->aktif['nomor_kredit']);
				$nota_b->tanggal 		= $this->tanggal;
				$nota_b->nip_karyawan 	= $this->nip_karyawan;
				$nota_b->jumlah 		= $this->formatMoneyTo($this->nominal);
				$nota_b->save();
	
				$nb 	= AngsuranDetail::whereIn('nth', array_column($tunggakan->toArray(), 'nth'))->update(['nota_bayar_id' => $nb->id]);
			}elseif($total < $this->nominal){
				//simpan nota bayar
				$nota_b = new NotaBayar;
				$nota_b->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$nota_b->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->aktif['nomor_kredit']);
				$nota_b->tanggal 		= $this->tanggal;
				$nota_b->nip_karyawan 	= $this->nip_karyawan;
				$nota_b->jumlah 		= $this->formatMoneyTo($total);
				$nota_b->save();

				$nb 	= AngsuranDetail::whereIn('nth', array_column($tunggakan->toArray(), 'nth'))->update(['nota_bayar_id' => $nb->id]);

				//simpan nota bayar
				$nota_b2 = new NotaBayar;
				$nota_b2->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$nota_b2->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->aktif['nomor_kredit']);
				$nota_b2->tanggal 		= $this->tanggal;
				$nota_b2->nip_karyawan 	= $this->nip_karyawan;
				$nota_b2->jumlah 		= $this->formatMoneyTo($this->nominal - $total);
				$nota_b2->save();
			}else{
				//simpan nota bayar
				$nota_b = new NotaBayar;
				$nota_b->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$nota_b->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->aktif['nomor_kredit']);
				$nota_b->tanggal 		= $this->tanggal;
				$nota_b->nip_karyawan 	= $this->nip_karyawan;
				$nota_b->jumlah 		= $this->formatMoneyTo($this->nominal);
				$nota_b->save();
			}
		}
		return true;
	}
}