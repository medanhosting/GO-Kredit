<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Kredit\Models\PermintaanRestitusi;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;

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
			$pr->jumlah 	= $this->formatMoneyTo($this->hitung_restitusi_3_hari($this->tanggal, $this->kredit['persentasi_denda']));
		}else{
			$pr->jenis 		= 'restitusi_nominal';
			$pr->jumlah 	= $nominal;
		}

		$pr->nomor_kredit 	= $this->kredit['nomor_kredit'];
		$pr->tanggal 		= $this->tanggal;
		$pr->alasan 		= $alasan;
		$pr->save();
	}

	private function hitung_restitusi_3_hari($tanggal, $persentasi_denda){
		$tunggakan 		= JadwalAngsuran::HistoriTunggakan(Carbon::createfromformat('d/m/Y H:i', $tanggal))->groupby('nth')->orderby('nth', 'asc')->selectraw('sum(jumlah) as tunggakan')->first();

		return ($tunggakan['tunggakan'] * $persentasi_denda * 3)/100;
	}

	public static function hitung_r3d($tanggal, $persentasi_denda){
		$tunggakan 		= JadwalAngsuran::HistoriTunggakan(Carbon::createfromformat('d/m/Y H:i', $tanggal))->groupby('nth')->orderby('nth', 'asc')->selectraw('sum(jumlah) as tunggakan')->first();

		return ($tunggakan['tunggakan'] * $persentasi_denda * 3)/100;
	}
	
	public function validasi_restitusi($is_approved){
		$pr 				= PermintaanRestitusi::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('is_approved')->first();
		$pr->karyawan		= $this->karyawan;
		$pr->is_approved	= $is_approved * 1;
		$pr->save();
	}

	public function bayar($nominal){
		$nb 	= new NotaBayar;
		$nb->nomor_faktur 		 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
		$nb->morph_reference_id 	= $this->kredit['nomor_kredit'];
		$nb->morph_reference_tag 	= 'kredit';
		$nb->tanggal 				= $this->tanggal;
		$nb->karyawan 				= $this->karyawan;
		$nb->jumlah 				= $nominal;
		$nb->jenis 					= 'denda';
		$nb->save();

		$angs 	= new DetailTransaksi;
		$angs->nomor_faktur 	= $nb->nomor_faktur;
		$angs->tag 				= 'denda';
		$angs->jumlah 			= $nominal;
		$angs->deskripsi 		= 'Bayar Denda';
		$angs->save();
	}
}