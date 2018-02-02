<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;

use App\Service\Traits\IDRTrait;

use Carbon\Carbon, Config;

use App\Exceptions\AppException;

class BayarAngsuran
{
	use IDRTrait;

	public function __construct(Aktif $aktif, $karyawan, $nth, $tanggal, $nomor_perkiraan = null){
		$this->kredit 			= $aktif;
		$this->karyawan 		= $karyawan;
		$this->nth 				= $nth;
		$this->tanggal 			= $tanggal;
		$this->nomor_perkiraan 	= $nomor_perkiraan;
	}

	public function bayar(){
		//check potongan
		$nth_dibayar 	= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenotnull('nomor_faktur')->count();

		$potongan 		= 'Rp 0';

		if($this->kredit['jangka_waktu'] == $nth_dibayar + count($this->nth)){
			$potongan 	= PelunasanAngsuran::potongan($this->kredit['nomor_kredit']);
		}

		$angsurans 		= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->whereIn('nth', $this->nth)->get();
		
		$angsuran 		= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->whereIn('nth', $this->nth)->sum('jumlah');

		$total 	= $angsuran + $this->formatMoneyFrom($titipan) + $this->formatMoneyFrom($potongan);

		//get rincian

		if(str_is($this->kredit['jenis_pinjaman'], 'pa'))
		{
			$rincian 	= new PerhitunganBunga($this->kredit['plafon_pinjaman'], 'Rp 0', $this->kredit['suku_bunga'], null, null, null, $this->kredit['jangka_waktu']);
			$rincian 	= $rincian->pa();
		}
		elseif(str_is($this->kredit['jenis_pinjaman'], 'pt'))
		{
			$rincian 	= new PerhitunganBunga($this->kredit['plafon_pinjaman'], 'Rp 0', $this->kredit['suku_bunga'], null, null, null, $this->kredit['jangka_waktu']);
			$rincian 	= $rincian->pt();
		}

		//simpan nota bayar
		$nb 		= NotaBayar::where('nomor_faktur', $angsurans[0]['nomor_faktur'])->first();
		if(!$nb){
			$nb 				= new NotaBayar;
			$nb->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
		}
		$nb->morph_reference_id 	= $this->kredit['nomor_kredit'];
		$nb->morph_reference_tag 	= 'kredit';
		$nb->tanggal 				= $this->tanggal;
		$nb->karyawan 				= $this->karyawan;
		$nb->jumlah 				= $this->formatMoneyTo($total);
		$nb->jenis 					= 'angsuran';
		$nb->save();

		//BAYAR DENGAN TITIPAN

		//simpan detail
		foreach ($angsurans as $k => $v) {
			if($rincian['angsuran'][$v['nth']]['angsuran_pokok']!='Rp 0'){
				//check titipan
				$titipan	= DetailTransaksi::whereIn('tag', ['titipan_pokok', 'restitusi_titipan_pokok'])->wherehas('notabayar', function($q){$q->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit');})->sum('jumlah');

				$hutang 	= $this->formatMoneyFrom($rincian['angsuran'][$v['nth']]['angsuran_pokok']);

				if($titipan > 0){
					$deskripsi 	= 'Pokok Angsuran Ke-'.$v['nth'];
					$angs 		= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('deskripsi', $deskripsi)->first();
					if(!$angs){
						$angs 	= new DetailTransaksi;
					}
					$angs->nomor_faktur 	= $nb->nomor_faktur;
					$angs->tag 				= 'restitusi_titipan_pokok';
					$angs->jumlah 			= $this->formatMoneyTo(0 - min($titipan, $hutang));
					$angs->deskripsi 		= $deskripsi;
					$angs->save();
					$hutang 	= $hutang - $titipan;

				}

				if($hutang > 0){
					$deskripsi 	= 'Pokok Angsuran Ke-'.$v['nth'];
					$angs 		= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('deskripsi', $deskripsi)->first();
					if(!$angs){
						$angs 	= new DetailTransaksi;
					}
					$angs->nomor_faktur 	= $nb->nomor_faktur;
					$angs->tag 				= 'pokok';
					$angs->jumlah 			= $this->formatMoneyTo($hutang);
					$angs->deskripsi 		= $deskripsi;
					$angs->save();
				}
			}

			if($rincian['angsuran'][$v['nth']]['angsuran_bunga']!='Rp 0'){
				//check titipan
				$titipan	= DetailTransaksi::whereIn('tag', ['titipan_bunga', 'restitusi_titipan_bunga'])->wherehas('notabayar', function($q){$q->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit');})->sum('jumlah');

				$hutang 	= $this->formatMoneyFrom($rincian['angsuran'][$v['nth']]['angsuran_bunga']);

				if($titipan > 0){
					$deskripsi 	= 'Bunga Angsuran Ke-'.$v['nth'];
					$angs 		= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('deskripsi', $deskripsi)->first();
					if(!$angs){
						$angs 	= new DetailTransaksi;
					}
					$angs->nomor_faktur 	= $nb->nomor_faktur;
					$angs->tag 				= 'restitusi_titipan_bunga';
					$angs->jumlah 			= $this->formatMoneyTo(0 - min($titipan, $hutang));
					$angs->deskripsi 		= $deskripsi;
					$angs->save();
					$hutang 	= $hutang - $titipan;
				}

				if($hutang > 0){
					$deskripsi 	= 'Bunga Angsuran Ke-'.$v['nth'];
					$angs 		= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('deskripsi', $deskripsi)->first();
					if(!$angs){
						$angs 	= new DetailTransaksi;
					}
					$angs->nomor_faktur 	= $nb->nomor_faktur;
					$angs->tag 				= 'bunga';
					$angs->jumlah 			= $this->formatMoneyTo($hutang);
					$angs->deskripsi 		= $deskripsi;
					$angs->save();
				}
			}
			
			//update nomor faktur
			$v->nomor_faktur 	= $nb->nomor_faktur;
			$v->save();
		}

		if($potongan != 'Rp 0'){
			$deskripsi 	= 'Potongan Pelunasan Angsuran';
			$angs 		= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('deskripsi', $deskripsi)->first();
			if(!$angs){
				$angs 	= new DetailTransaksi;
			}
			$angs->nomor_faktur 	= $nb->nomor_faktur;
			$angs->tag 				= 'potongan';
			$angs->jumlah 			= $potongan;
			$angs->deskripsi 		= $deskripsi;
			$angs->save();
		}
	}

	public function bayar_sebagian($nominal){
		$nth_belum_bayar 	= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->first();

		$jlh_b_bayar 	= $this->formatMoneyFrom($nth_belum_bayar->jumlah);
		$titipan 		= $this->formatMoneyFrom($nominal);

		if($jlh_b_bayar > $titipan){
			//simpan tanda terima
			$nb 				= new NotaBayar;
			$nb->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
			$nb->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$nb->morph_reference_tag 	= 'kredit';
			$nb->tanggal 				= $this->tanggal;
			$nb->karyawan 				= $this->karyawan;
			$nb->jumlah 				= $nominal;
			$nb->jenis 					= 'angsuran_sementara';
			$nb->save();

			if(str_is($this->kredit['jenis_pinjaman'], 'pa'))
			{
				$rincian 	= new PerhitunganBunga($this->kredit['plafon_pinjaman'], 'Rp 0', $this->kredit['suku_bunga'], null, null, null, $this->kredit['jangka_waktu']);
				$rincian 	= $rincian->pa();
				$angs_b 	= $this->formatMoneyFrom($rincian['angsuran'][$nth_belum_bayar['nth']]['angsuran_bunga']);

				//simpan detail
				$deskripsi 	= 'Titipan Angsuran Bunga Ke-'.$nth_belum_bayar['nth'];
				$angs 		= new DetailTransaksi;
				$angs->nomor_faktur 	= $nb->nomor_faktur;
				$angs->tag 				= 'titipan_bunga';
				$angs->jumlah 			= $this->formatMoneyTo(min($titipan, $angs_b));
				$angs->deskripsi 		= $deskripsi;
				$angs->save();
				$titipan 	= $titipan - min($titipan, $angs_b);

				if($titipan > 0){
					//simpan detail
					$deskripsi 	= 'Titipan Angsuran Pokok Ke-'.$nth_belum_bayar['nth'];
					$angs 		= new DetailTransaksi;
					$angs->nomor_faktur 	= $nb->nomor_faktur;
					$angs->tag 				= 'titipan_pokok';
					$angs->jumlah 			= $this->formatMoneyTo($titipan);
					$angs->deskripsi 		= $deskripsi;
					$angs->save();
				}
			}
			else{
				//simpan detail
				$deskripsi 	= 'Titipan Angsuran Bunga Ke-'.$nth_belum_bayar['nth'];
				$angs 		= new DetailTransaksi;
				$angs->nomor_faktur 	= $nb->nomor_faktur;
				$angs->tag 				= 'titipan_bunga';
				$angs->jumlah 			= $nominal;
				$angs->deskripsi 		= $deskripsi;
				$angs->save();
			}
		}else{
			throw new AppException(['nominal' => 'Pembayaran Cukup Untuk Melunasi 1 Angsuran'], 1);
		}
	}
}