<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\Penagihan;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Finance\Models\Jurnal;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\WaktuTrait;
use App\Service\System\Calculator;

use Carbon\Carbon, Config;

use App\Exceptions\AppException;

use App\Service\System\PerhitunganBayar;

class BayarAngsuran
{
	use IDRTrait;
	use WaktuTrait;

	public function __construct(Aktif $aktif, $karyawan, $jumlah, $nth, $tanggal, $nomor_perkiraan = null){
		$this->kredit 			= $aktif;
		$this->karyawan 		= $karyawan;
		$this->jumlah 			= $jumlah;
		$this->nth 				= $nth;
		$this->tanggal 			= Carbon::createFromFormat('d/m/Y H:i', $tanggal);
		$this->nomor_perkiraan 	= $nomor_perkiraan;
	}

	public function bayar(){
		$tanggal 	= Carbon::parse($this->tanggal);
		$bayar 		= new PerhitunganBayar($this->kredit['nomor_kredit'], $tanggal, $this->nth, $this->jumlah);

		$this->buat_faktur($this->kredit['nomor_kredit'], 'kredit', 'angsuran');
		$this->buat_detail_faktur($bayar, $this->notabayar);

		$nth_akan_d = JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->wherein('nth', $this->add_nth)->update(['nomor_faktur' => $this->notabayar->nomor_faktur]);

		$nth_akan_d = JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->orderby('nth', 'asc')->wherein('nth', $this->nth)->update(['tanggal_bayar' => $this->tanggal->format('Y-m-d H:i:s')]);

		return true;
	}

	public function bayar_sebagian($nominal){

		return $this->bayar();
	}

	public function penerimaan_kas_kolektor($nomor_faktur){
		$tagih 				= Penagihan::where('nomor_faktur', $nomor_faktur)->first();
		if($tagih){
			$tagih->tag 	= "completed";
			$tagih->save();
		}

		$tanggal	= Carbon::parse($this->tanggal);
		$nb			= NotaBayar::where('nomor_faktur', $nomor_faktur)->first();

		$bayar 		= new PerhitunganBayar($this->kredit['nomor_kredit'], $tanggal, $this->nth, $nb['jumlah']);

		//simpan nb baru
		$this->buat_faktur($nomor_faktur, 'finance', 'memorial_kolektor');
		$this->buat_detail_faktur($bayar, $this->notabayar);

		//update jadwal t
		$nth_akan_d = JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->wherein('nth', $this->add_nth)->update(['nomor_faktur' => $this->notabayar->nomor_faktur]);

		$nth_akan_d = JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->orderby('nth', 'asc')->wherein('nth', $this->nth)->update(['tanggal_bayar' => $this->tanggal->format('Y-m-d H:i:s')]);

		return true;
	}

	private function buat_faktur($ref_id, $ref_tag, $jenis){
		//simpan nota bayar
		$nb 				= new NotaBayar;
		$nb->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
		$nb->morph_reference_id 	= $ref_id;
		$nb->morph_reference_tag 	= $ref_tag;
		$nb->nomor_rekening 		= $this->nomor_perkiraan;
		$nb->tanggal 				= $this->tanggal->format('d/m/Y H:i');
		$nb->karyawan 				= $this->karyawan;
		$nb->jumlah 				= $this->jumlah;
		$nb->jenis 					= $jenis;
		$nb->save();

		$this->notabayar 			= $nb;
	}

	private function buat_detail_faktur(PerhitunganBayar $bayar, NotaBayar $notabayar){

		if(str_is($this->kredit['jenis_pinjaman'], 'pa')){
			$bayar 	= $bayar->pa();
		}elseif(str_is($this->kredit['jenis_pinjaman'], 'pt')){
			$bayar 	= $bayar->pt();
		}

		$faktur 	= PerhitunganBayar::generateFaktur($this->kredit['nomor_kredit'], $bayar, $this->tanggal);

		if($faktur['balance_titipan'] > 0){
			//simpan balance titipan
			$angs 		= new DetailTransaksi;
			$angs->nomor_faktur		= $notabayar->nomor_faktur;
			$angs->tag 				= 'titipan';
			$angs->jumlah 			= $this->formatMoneyTo($faktur['balance_titipan']);
			$angs->deskripsi 		= 'Titipan Angsuran';
			$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$angs->morph_reference_tag 	= 'kredit';
			$angs->save();
		}

		foreach ($faktur['isi'] as $k => $v) {
			$angs 		= new DetailTransaksi;
			$angs->fill($v);
			$angs->nomor_faktur 		= $notabayar->nomor_faktur;
			$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$angs->morph_reference_tag 	= 'kredit';
			$angs->save();
		}

		$notabayar->jumlah 		= $this->formatMoneyTo($faktur['total']);
		$notabayar->save();

		$this->nth 				= $faktur['nth_jt'];
		$this->add_nth 			= $faktur['nth'];
	}
}