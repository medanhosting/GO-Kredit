<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;

use App\Service\Traits\IDRTrait;

use Carbon\Carbon, Config;

use App\Exceptions\AppException;

class BayarAngsuran
{
	use IDRTrait;

	public function __construct(Aktif $aktif, $nip_karyawan, $nth, $tanggal, $nomor_perkiraan = null){
		$this->kredit 			= $aktif;
		$this->nip_karyawan 	= $nip_karyawan;
		$this->nth 				= $nth;
		$this->tanggal 			= $tanggal;
		$this->nomor_perkiraan 	= $nomor_perkiraan;
	}

	public function bayar(){
		///check titipan
		$titipan 	= AngsuranDetail::where('nomor_kredit', $this->kredit['nomor_kredit'])->whereIn('tag', ['titipan', 'pengambilan_titipan'])->sum('amount');

		$angsuran 	= AngsuranDetail::whereIn('nth', $this->nth)->whereIn('tag', ['bunga', 'pokok'])->where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nota_bayar_id')->get();

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
			$nb->nomor_perkiraan= $this->nomor_perkiraan;
			$nb->jenis 			= 'angsuran';
			$nb->save();

			foreach ($angsuran as $k => $v) {
				if(is_null($v->nota_bayar_id)){
					$v->nota_bayar_id 	= $nb->id;
					$v->save();
				}
			}

			if($titipan > 0){
				//simpan notabayar
				$nbti 					= new NotaBayar;
				$nbti->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
				$nbti->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$nbti->tanggal 			= $this->tanggal;
				$nbti->nip_karyawan 	= $this->nip_karyawan;
				$nbti->jumlah 			= $this->formatMoneyTo(0 - min($titipan, $total_pay));
				$nbti->nomor_perkiraan 	= Config::get('finance.nomor_perkiraan_titipan');
				$nbti->jenis 			= 'pengambilan_titipan';
				$nbti->save();

				$pad 	= new AngsuranDetail;
				$pad->nota_bayar_id	= $nbti->id;
				$pad->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$pad->tanggal 		= $this->tanggal;
				$pad->nth 			= $latest_pay['nth'] + 1;
				$pad->tag 			= 'pengambilan_titipan';
				$pad->amount 		= $this->formatMoneyTo(0 - min($titipan, $total_pay));
				$pad->description 	= 'Pengambilan Titipan';
				$pad->save();
			}

			if($potongan){
				$pad 	= new AngsuranDetail;
				$pad->nota_bayar_id	= $nb->id;
				$pad->nomor_kredit 	= $this->kredit['nomor_kredit'];
				$pad->tanggal 		= $this->tanggal;
				$pad->nth 			= $total;
				$pad->tag 			= 'potongan';
				$pad->amount 		= $potongan;
				$pad->description 	= 'Potongan Pelunasan';
				$pad->save();
			}

		}
	}

	public function bayar_sebagian($nominal){
		$angsuran 	= AngsuranDetail::whereIn('tag', ['bunga', 'pokok'])->where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nota_bayar_id')->selectraw('sum(amount) as total')->selectraw('nth')->groupby('nth')->orderby('nth', 'asc')->first();

		$titipan 	= AngsuranDetail::where('nomor_kredit', $this->kredit['nomor_kredit'])->whereIn('tag', ['titipan', 'pengambilan_titipan'])->sum('amount');

		if(!$angsuran){
			throw new AppException(['nominal' => 'Tidak Ada Angsuran Lagi'], 1);
		}
		elseif(($angsuran['total'] - $titipan) > $this->formatMoneyFrom($nominal)){
			$nb 		= new NotaBayar;
			$nb->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
			$nb->nomor_kredit 	= $this->kredit['nomor_kredit'];
			$nb->tanggal 		= $this->tanggal;
			$nb->nip_karyawan 	= $this->nip_karyawan;
			$nb->jumlah 		= $nominal;
			$nb->nomor_perkiraan= $this->nomor_perkiraan;
			$nb->jenis 			= 'angsuran_sementara';
			$nb->save();

			$pad 		= new AngsuranDetail;
			$pad->nota_bayar_id	= $nb->id;
			$pad->nomor_kredit 	= $this->kredit['nomor_kredit'];
			$pad->tanggal 		= $this->tanggal;
			$pad->nth 			= $angsuran['nth'];
			$pad->tag 			= 'titipan';
			$pad->amount 		= $nominal;
			$pad->description 	= 'Titipan Melalui Kasir';
			$pad->save();
		}else{
			throw new AppException(['nominal' => 'Pembayaran Cukup Untuk Melunasi 1 Angsuran'], 1);
		}
	}
}