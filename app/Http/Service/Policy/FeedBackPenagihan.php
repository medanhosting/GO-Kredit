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
		$sp 		= SuratPeringatan::where('nomor_kredit', $this->kredit['nomor_kredit'])->where('id', $this->sp_id)->first();
		$jumlah 	= $this->nominal;
		//simpan titipan
		if($this->nominal * 1 > 0){

			if(str_is($this->kredit['jenis_pinjaman'], 'pa')){
				$this->buat_faktur();
				$this->hitung_pa();
			}
			elseif(str_is($this->kredit['jenis_pinjaman'], 'pt') && $sp['nth'] < 6){
				$this->buat_faktur();
				$this->hitung_pt();
			}
		}

		$tagih 		= new Penagihan;
		$tagih->surat_peringatan_id	= $sp->id;
		$tagih->nomor_kredit 		= $this->kredit['nomor_kredit'];
		if(isset($this->nomor_faktur)){
			$tagih->nomor_faktur 	= $this->nomor_faktur;
		}
		$tagih->tag 				= 'completing';
		$tagih->penerima 			= $this->penerima;
		$tagih->jumlah 				= $this->formatMoneyTo($jumlah);
		$tagih->tanggal 			= $this->tanggal;
		$tagih->karyawan 			= $this->karyawan;
		$tagih->save();

		return true;
	}

	public function penerimaan_titipan_tagihan($penagihan_id){
		$tagih 			= Penagihan::where('id', $penagihan_id)->first();
		$tagih->tag 	= "completed";
		$tagih->save();
	}

	private function hitung_pa(){
		$rincian 	= new PerhitunganBunga($this->kredit['plafon_pinjaman'], 'Rp 0', $this->kredit['suku_bunga'], null, null, null, $this->kredit['jangka_waktu']);
		$rincian 	= $rincian->pa();

		while ($this->nominal > 0) {
			$nth_belum_bayar 	= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->first();
			$angs_b 	= $this->formatMoneyFrom($rincian['angsuran'][$nth_belum_bayar['nth']]['angsuran_bunga']);
			$angs_p 	= $this->formatMoneyFrom($rincian['angsuran'][$nth_belum_bayar['nth']]['angsuran_pokok']);

			//simpan detail
			$deskripsi 	= 'Titipan Angsuran Bunga Ke-'.$nth_belum_bayar['nth'];
			$angs 		= new DetailTransaksi;
			$angs->nomor_faktur 	= $this->nomor_faktur;
			$angs->tag 				= 'titipan_bunga';
			$angs->jumlah 			= $this->formatMoneyTo(min($this->nominal, $angs_b));
			$angs->deskripsi 		= $deskripsi;
			$angs->save();
			$this->nominal 	= $this->nominal - min($this->nominal, $angs_b);

			if($this->nominal > 0){
				//simpan detail
				$deskripsi 	= 'Titipan Angsuran Pokok Ke-'.$nth_belum_bayar['nth'];
				$angs 		= new DetailTransaksi;
				$angs->nomor_faktur 	= $this->nomor_faktur;
				$angs->tag 				= 'titipan_pokok';
				$angs->jumlah 			= $this->formatMoneyTo(min($this->nominal, $angs_p));
				$angs->deskripsi 		= $deskripsi;
				$angs->save();
				$this->nominal 	= $this->nominal - min($this->nominal, $angs_p);
			}
		}
	}
	
	private function hitung_pt(){
		$rincian 	= new PerhitunganBunga($this->kredit['plafon_pinjaman'], 'Rp 0', $this->kredit['suku_bunga'], null, null, null, $this->kredit['jangka_waktu']);
		$rincian 	= $rincian->pa();
		$angs_b 	= $this->formatMoneyFrom($rincian['angsuran'][0]['angsuran_bunga']);

		while ($this->nominal > 0) {
			//simpan detail
			$deskripsi 	= 'Titipan Angsuran Bunga Ke-'.$nth_belum_bayar['nth'];
			$angs 		= new DetailTransaksi;
			$angs->nomor_faktur 	= $this->nomor_faktur;
			$angs->tag 				= 'titipan_bunga';
			$angs->jumlah 			= $this->formatMoneyTo(min($this->nominal, $angs_b));
			$angs->deskripsi 		= $deskripsi;
			$angs->save();
			$this->nominal 	= $this->nominal - min($this->nominal, $angs_b);
		}
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
		$nb->jenis 					= 'angsuran_sementara';
		$nb->save();

		$this->nomor_faktur 	= $nb->nomor_faktur;
	}
}