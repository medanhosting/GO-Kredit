<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\NotaBayar;
use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\PermintaanRestitusi;

use App\Service\Traits\IDRTrait;

use Carbon\Carbon, Exception;

class BayarDenda
{
	use IDRTrait;

	public function __construct(Aktif $aktif, $karyawan, $tanggal, $nomor_perkiraan = null){
		$this->kredit 			= $aktif;
		$this->karyawan 		= $karyawan;
		$this->tanggal 			= $tanggal;
		$this->nomor_perkiraan	= $nomor_perkiraan;
	}

	public function permintaan_restitusi($jenis, $nominal, $alasan){
		$pr 	= PermintaanRestitusi::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('is_approved')->first();
		if(!$pr){
			$pr = new PermintaanRestitusi;
		}

		if(str_is('restitusi_3_hari', $jenis)){
			$pr->jenis 		= $jenis;
			$tunggakan		= AngsuranDetail::whereIn('tag', ['pokok', 'bunga'])->wherenull('nota_bayar_id')->where('nomor_kredit', $this->kredit['nomor_kredit'])->where('tanggal', '<=', Carbon::createfromformat('d/m/Y H:i', $this->tanggal)->format('Y-m-d H:i:s'))->sum('amount');
			$pr->amount 	= $this->formatMoneyTo(($tunggakan * $this->kredit['persentasi_denda'] * 3)/100);
		}else{
			$pr->jenis 		= 'restitusi_nominal';
			$pr->amount 	= $nominal;
		}

		$pr->nomor_kredit 	= $this->kredit['nomor_kredit'];
		$pr->tanggal 		= $this->tanggal;
		$pr->alasan 		= $alasan;
		$pr->save();
	}

	public function validasi_restitusi($is_approved){
		$pr 			= PermintaanRestitusi::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('is_approved')->first();

		if($pr && ($is_approved * 1)){
			$denda 		= AngsuranDetail::whereIn('tag', ['denda'])->where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nota_bayar_id')->orderby('tanggal', 'desc')->first();
		
			$t_denda 	= AngsuranDetail::whereIn('tag', ['denda', 'restitusi_denda'])->where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nota_bayar_id')->sum('amount');
			
			$pad 	= new AngsuranDetail;
			$pad->nomor_kredit 	= $this->kredit['nomor_kredit'];
			$pad->tanggal 		= $this->tanggal;
			$pad->nth 			= $denda['nth'];
			$pad->tag 			= 'restitusi_denda';
			$pad->amount 		= $this->formatMoneyTo(0 - $this->formatMoneyFrom($pr['amount']));
			$pad->description 	= 'Persetujuan Restitusi';
			$pad->save();
		}

		$pr->karyawan 				= $this->karyawan;
		$pr->is_approved 			= $is_approved * 1;
		$pr->save();
	}

	public function bayar(){
		$amount 	= AngsuranDetail::whereIn('tag', ['denda', 'restitusi_denda'])->where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nota_bayar_id')->sum('amount');

		$first 		= AngsuranDetail::whereIn('tag', ['denda'])->where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nota_bayar_id')->orderby('nth', 'asc')->first();

		$denda 		= AngsuranDetail::whereIn('tag', ['denda', 'restitusi_denda'])->where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nota_bayar_id')->get();

		$nb 	= new NotaBayar;
		$nb->nomor_faktur 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
		$nb->nomor_kredit 	= $this->kredit['nomor_kredit'];
		$nb->tanggal 		= $this->tanggal;
		$nb->nip_karyawan 	= $this->karyawan['nip'];
		$nb->nomor_perkiraan= $this->nomor_perkiraan;
		$nb->jumlah 		= $this->formatMoneyTo($amount);
		$nb->save();

		foreach ($denda as $k => $v) {
			$v->nota_bayar_id = $nb->id;
			$v->save();
		}
	}
}