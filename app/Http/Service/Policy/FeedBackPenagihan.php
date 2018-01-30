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

	public function __construct(Aktif $aktif, $karyawan, $tanggal, $penerima, $nominal = null, $nomor_perkiraan = null, $sp_id = null){
		$this->kredit 			= $aktif;
		$this->karyawan 		= $karyawan;
		$this->tanggal 			= $tanggal;
		$this->penerima 		= $penerima;
		$this->nominal 			= $this->formatMoneyFrom($nominal);
		$this->nomor_perkiraan 	= $nomor_perkiraan;
		$this->sp_id 			= $sp_id;
	}

	public function bayar(){
		$find_exists_sp 		= SuratPeringatan::where('nomor_kredit', $this->kredit['nomor_kredit'])->where('id', $this->sp_id)->first();

		$tagih 		= new Penagihan;
		$tagih->karyawan 		= $this->karyawan;
		$tagih->tanggal 		= $this->tanggal;
		$tagih->penerima 		= $this->penerima;
		$tagih->nomor_kredit 	= $this->kredit['nomor_kredit'];
		$tagih->tag 			= 'completed';
		$tagih->save();

		$find_exists_sp->penagihan_id 	= $tagih->id;
		$find_exists_sp->save();

		if(!is_null($this->nominal)){

			//check tunggakan
			$today		= Carbon::now();
			$tunggakan  = AngsuranDetail::displaying()->where('nomor_kredit', $this->kredit['nomor_kredit'])->where('tanggal', '<=', $today->format('Y-m-d H:i:s'))->get();

			//simpan nota bayar
			$nota_b = new NotaBayar;
			$nota_b->nomor_kredit 	= $this->kredit['nomor_kredit'];
			$nota_b->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
			$nota_b->tanggal 		= $this->tanggal;
			$nota_b->nip_karyawan 	= $this->karyawan['nip'];
			$nota_b->nomor_perkiraan= $this->nomor_perkiraan;
			$nota_b->penagihan_id 	= $tagih->id;
			$nota_b->jumlah 		= $this->formatMoneyTo($this->nominal);
			$nota_b->jenis 			= 'angsuran_sementara';
			$nota_b->save();

			$titipan 	= new AngsuranDetail;
			$titipan->nota_bayar_id 	= $nota_b->id;
			$titipan->nomor_kredit 		= $this->kredit['nomor_kredit'];
			$titipan->tanggal 			= $this->tanggal;
			$titipan->nth  				= $tunggakan[0]['nth'];
			$titipan->tag 				= 'titipan';
			$titipan->amount 			= $this->formatMoneyTo($this->nominal);
			$titipan->description 		= 'Titipan tagihan kredit nomor '.$this->kredit['nomor_kredit'];
			$titipan->save();

			$tagih->nota_bayar_id 	= $nota_b->id;
			$tagih->save();
		}
		return true;
	}

	public function penerimaan_titipan_tagihan($nota_bayar_id){
		$nota_b = NotaBayar::where('id', $nota_bayar_id)->first();
		$nota_b->nomor_perkiraan 	= $this->nomor_perkiraan;
		$nota_b->save();
	}
}