<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Kredit\Models\PermintaanRestitusi;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;

use App\Service\System\Calculator;

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
			$pr->jumlah 	= $this->formatMoneyTo(Calculator::restitusi3DBefore($this->kredit['nomor_kredit'], Carbon::createfromformat('d/m/Y H:i', $this->tanggal)->adddays(1)));
		}else{
			$pr->jenis 		= 'restitusi_nominal';
			$pr->jumlah 	= $nominal;
		}

		$pr->nomor_kredit 	= $this->kredit['nomor_kredit'];
		$pr->tanggal 		= $this->tanggal;
		$pr->alasan 		= $alasan;
		$pr->karyawan		= $this->karyawan;
		$pr->save();
	}

	public function validasi_restitusi($is_approved){
		$pr 				= PermintaanRestitusi::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('is_approved')->first();

		if($is_approved * 1){
			$nominal 	= $this->formatMoneyTo(0 - $this->formatMoneyFrom($pr->jumlah));

			//simpan restitusi
			$nb 	= new NotaBayar;
			$nb->nomor_faktur 		 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
			$nb->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$nb->morph_reference_tag 	= 'kredit';
			$nb->tanggal 				= $this->tanggal;
			$nb->karyawan 				= $this->karyawan;
			$nb->jumlah 				= $nominal;
			$nb->jenis 					= 'restitusi_denda';
			$nb->save();

			$angs 	= new DetailTransaksi;
			$angs->nomor_faktur 	= $nb->nomor_faktur;
			$angs->tag 				= 'restitusi_denda';
			$angs->jumlah 			= $nominal;
			$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$angs->morph_reference_tag 	= 'kredit';
			$angs->deskripsi 		= ucwords(str_replace('_', ' ', str_replace('restitusi', 'restitusi_denda', $pr->jenis)));
			$angs->save();
			
			$pr->nomor_faktur	= $nb->nomor_faktur;
		}

		$pr->is_approved	= $is_approved * 1;
		$pr->save();
	}

	public function bayar($nominal){
		$nb 	= new NotaBayar;
		$nb->nomor_faktur 		 	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
		$nb->morph_reference_id 	= $this->kredit['nomor_kredit'];
		$nb->morph_reference_tag 	= 'kredit';
		$nb->nomor_rekening			= $this->nomor_perkiraan;
		$nb->tanggal 				= $this->tanggal;
		$nb->karyawan 				= $this->karyawan;
		$nb->jumlah 				= $nominal;
		$nb->jenis 					= 'denda';
		$nb->save();

		$angs 	= new DetailTransaksi;
		$angs->nomor_faktur 	= $nb->nomor_faktur;
		$angs->tag 				= 'denda';
		$angs->jumlah 			= $nominal;
		$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
		$angs->morph_reference_tag 	= 'kredit';
		$angs->deskripsi 		= 'Bayar Denda';
		$angs->save();
	}
}