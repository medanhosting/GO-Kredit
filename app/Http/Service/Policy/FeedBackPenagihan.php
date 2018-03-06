<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;
use Thunderlabid\Kredit\Models\SuratPeringatan;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\WaktuTrait;

use App\Exceptions\AppException;

use App\Service\System\Calculator;

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
		//simpan titipan
		if($this->nominal * 1 > 0){
			$this->buat_faktur();
			$this->buat_detail_faktur();
		}

		//update sp
		$sp 		= SuratPeringatan::where('nomor_kredit', $this->kredit['nomor_kredit'])->where('id', $this->sp_id)->first();

		$tagih 		= new Penagihan;
		$tagih->nomor_kredit 		= $this->kredit['nomor_kredit'];
		
		if($sp){
			$tagih->surat_peringatan_id	= $sp->id;
		}
		if(isset($this->nomor_faktur)){
			$tagih->nomor_faktur 		= $this->nomor_faktur;
		}

		$tagih->tag 				= 'completing';
		$tagih->penerima 			= $this->penerima;
		$tagih->jumlah 				= $this->formatMoneyTo($this->nominal);
		$tagih->tanggal 			= $this->tanggal;
		$tagih->karyawan 			= $this->karyawan;
		$tagih->save();

		return $tagih;
	}

	private function buat_faktur(){
		//simpan nota bayar
		$nb 				= new NotaBayar;
		$nb->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
		$nb->morph_reference_id 	= $this->kredit['nomor_kredit'];
		$nb->morph_reference_tag 	= 'kredit';
		$nb->tanggal 				= $this->tanggal;
		$nb->karyawan 				= $this->karyawan;
		$nb->jumlah 				= $this->formatMoneyTo($this->nominal);
		$nb->jenis 					= 'kolektor';
		$nb->save();

		$this->nomor_faktur 	= $nb->nomor_faktur;
	}

	private function buat_detail_faktur(){
		$deskripsi 	= 'Pembayaran Angsuran Melalui Kolektor';
		$angs 		= new DetailTransaksi;
		$angs->nomor_faktur 	= $this->nomor_faktur;
		$angs->tag 				= 'kolektor';
		$angs->jumlah 			= $this->formatMoneyTo($this->nominal);
		$angs->deskripsi 		= $deskripsi;
		$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
		$angs->morph_reference_tag 	= 'kredit';
		$angs->save();
	}
}