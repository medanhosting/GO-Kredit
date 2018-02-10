<?php

namespace App\Http\Service\Policy;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;
use Thunderlabid\Finance\Models\Jurnal;
use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\WaktuTrait;

use Carbon\Carbon, Config;

use App\Exceptions\AppException;

class BayarAngsuran
{
	use IDRTrait;
	use WaktuTrait;

	public function __construct(Aktif $aktif, $karyawan, $nth, $tanggal, $nomor_perkiraan = null){
		$this->kredit 			= $aktif;
		$this->karyawan 		= $karyawan;
		$this->nth 				= $nth;
		$this->tanggal 			= $tanggal;
		$this->nomor_perkiraan 	= $nomor_perkiraan;
	}

	public function bayar(){
		$total 		= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->skip(0)->take($this->nth)->sum('jumlah');
		$nth_akan_d = JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->skip(0)->take($this->nth)->get();

		$belum_b 	= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->skip($this->nth)->take(1)->get();

		$potongan 	= 0;

		if(!count($belum_b)){

			$potongan 	= $this->formatMoneyFrom(PelunasanAngsuran::potongan($this->kredit['nomor_kredit']));
			$satu_angs 	= $this->formatMoneyFrom($nth_akan_d[0]['jumlah']);
			$satu_pokok = $this->formatMoneyFrom($nth_akan_d[0]['pokok']);
			$satu_bunga = $this->formatMoneyFrom($nth_akan_d[0]['bunga']);

			//artinya bayar lunas
			$total_titipan		= abs(Jurnal::whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['200.210']);})->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit')->sum('jumlah'));

			//1. Check TITIPAN
			if($total_titipan > 0 && $total_titipan%$satu_angs != 0){
				//1a. Kalau titipan kurang untuk lunasi, tambah titipan. tambah_titip = total - potongan;
				//simpan nota bayar
				$bm 				= new NotaBayar;
				$bm->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
				$bm->morph_reference_id 	= $this->kredit['nomor_kredit'];
				$bm->morph_reference_tag 	= 'kredit';
				$bm->tanggal 				= $this->tanggal;
				$bm->karyawan 				= $this->karyawan;
				$bm->jumlah 				= $this->formatMoneyTo(0);
				$bm->jenis 					= 'memorial';
				$bm->save();

				$deskripsi 	= 'Titipan Pembayaran Angsuran';
				$angs 		= new DetailTransaksi;
				$angs->nomor_faktur 	= $bm->nomor_faktur;
				$angs->tag 				= 'titipan';
				$angs->jumlah 			= $this->formatMoneyTo($satu_angs - ($total_titipan % $satu_angs));
				$angs->deskripsi 		= $deskripsi;
				$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
				$angs->morph_reference_tag 	= 'kredit';
				$angs->save();
			}

			$total_titipan		= abs(Jurnal::whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['200.210']);})->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit')->sum('jumlah'));
			
			$nb 				= new NotaBayar;
			$nb->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
			$nb->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$nb->morph_reference_tag 	= 'kredit';
			$nb->tanggal 				= $this->tanggal;
			$nb->karyawan 				= $this->karyawan;
			$nb->jumlah 				= $this->formatMoneyTo($total);
			$nb->jenis 					= 'angsuran';
			$nb->nomor_rekening 		= $this->nomor_perkiraan;
			$nb->save();
		
			//2. Simpan pokok
			//2a. jika ada titipan, tag jadi restitusi pokok
			//2b. jika tidak ada titipan, tag jadi pokok [simpan 1 saja]
			$pokok 		= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->sum('pokok');
			
			$piut_p 	= Jurnal::where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['120.300', '120.400']);})->sum('jumlah');

			//jika ada titipan pokok
			if($total_titipan > 0){
				//1. simpan titipan
				$bp[0]['tag'] 		= 'restitusi_titipan_pokok';
				$bp[0]['jumlah'] 	= ($total_titipan/$satu_angs) * $satu_pokok;
				$pengurang_p 		= ($total_titipan/$satu_angs) * $satu_pokok;

				//2. simpan piutang
				if(($piut_p - $pengurang_p) > 0){
					$bp[1]['tag'] 		= 'pokok';
					$bp[1]['jumlah'] 	= $piut_p - $pengurang_p;
					$pengurang_p 		= $pengurang_p + ($piut_p - $pengurang_p);
				}

				if(($piut_p - $bp[0]['jumlah']) < $pokok){
					$bp[2]['tag'] 	= 'pokok';
					$bp[2]['jumlah'] = $pokok - $pengurang_p;
				}
			}else{
				//2. simpan piutang
				if($piut_p > 0){
					$bp[0]['tag'] 	= 'pokok';
					$bp[0]['jumlah'] = $piut_p;
				}

				if($piut_p < $pokok){
					$bp[1]['tag'] 	= 'pokok';
					$bp[1]['jumlah'] = $pokok - $piut_p;
				}
			}

			foreach ($bp as $k => $v) {
				$deskripsi 	= 'Pelunasan Pokok Angsuran';
				$angs 		= new DetailTransaksi;
				$angs->nomor_faktur 	= $nb->nomor_faktur;
				$angs->tag 				= $v['tag'];
				$angs->jumlah 			= $this->formatMoneyTo($v['jumlah']);
				$angs->deskripsi 		= $deskripsi;
				$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
				$angs->morph_reference_tag 	= 'kredit';
				$angs->save();
			}


			//3. Simpan bunga
			//3a. jika ada titipan, tag jadi restitusi bunga
			//3b. jika tidak ada titipan, tag jadi bunga [simpan 1 saja], bunga = potongan
			$bunga 		= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->sum('bunga');

			$piut_b 	= Jurnal::where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit')->whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['140.100', '140.200']);})->sum('jumlah');

			//jika ada titipan pokok
			if($total_titipan > 0){
				//1. simpan titipan
				$bb[0]['tag'] 		= 'restitusi_titipan_bunga';
				$bb[0]['jumlah'] 	= $total_titipan;
				$pengurang_b 		= $total_titipan;

				//2. simpan piutang
				if(($piut_b - $total_titipan) > 0){
					$bb[1]['tag'] 		= 'bunga';
					$bb[1]['jumlah'] 	= $piut_b - $total_titipan;
					$pengurang_b		= $pengurang_b + ($piut_b - $total_titipan);
				}
				$total_titipan 		= 0;

				if(($piut_b - $bb[0]['jumlah']) < $bunga){
					$bb[2]['tag'] 	= 'bunga';
					$bb[2]['jumlah'] = ($bunga + $potongan) - $pengurang_b;
				}
			}else{
				//2. simpan piutang
				if($piut_b > 0){
					$bb[0]['tag'] 		= 'bunga';
					$bb[0]['jumlah'] 	= $piut_b;
				}

				if($piut_b < $bunga){
					$bb[1]['tag'] 	= 'bunga';
					$bb[1]['jumlah'] = ($bunga + $potongan) - $piut_b;
				}
			}

			foreach ($bb as $k => $v) {
				$deskripsi 	= 'Pelunasan Bunga Angsuran';
				$angs 		= new DetailTransaksi;
				$angs->nomor_faktur 	= $nb->nomor_faktur;
				$angs->tag 				= $v['tag'];
				$angs->jumlah 			= $this->formatMoneyTo($v['jumlah']);
				$angs->deskripsi 		= $deskripsi;
				$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
				$angs->morph_reference_tag 	= 'kredit';
				$angs->save();
			}

			$nth_akan_d = JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->skip(0)->take($this->nth)->update(['nomor_faktur' => $nb->nomor_faktur, 'tanggal_bayar' => $this->formatDateTimeFrom($this->tanggal)]);

			return true;
		}

		//1. Check TITIPAN
		//1a. Jika TITIPAN > 0, buat memorial untuk masuk titipan
		$total_titipan		= abs(Jurnal::whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['200.210']);})->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit')->sum('jumlah'));

		if($total_titipan > 0 && $total_titipan < $total){
			$jlh 	= $this->formatMoneyFrom($nth_akan_d[0]['jumlah']);
			$mod 	= $total_titipan%$jlh;

			if($mod > 0){
				//simpan nota bayar
				$bm 				= new NotaBayar;
				$bm->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
				$bm->morph_reference_id 	= $this->kredit['nomor_kredit'];
				$bm->morph_reference_tag 	= 'kredit';
				$bm->tanggal 				= $this->tanggal;
				$bm->karyawan 				= $this->karyawan;
				$bm->jumlah 				= $this->formatMoneyTo(0);
				$bm->jenis 					= 'memorial';
				$bm->save();

				$deskripsi 	= 'Titipan Pembayaran Angsuran';
				$angs 		= new DetailTransaksi;
				$angs->nomor_faktur 	= $bm->nomor_faktur;
				$angs->tag 				= 'titipan';
				$angs->jumlah 			= $this->formatMoneyTo($jlh - $mod);
				$angs->deskripsi 		= $deskripsi;
				$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
				$angs->morph_reference_tag 	= 'kredit';
				$angs->save();
			}
		}

		$total_titipan		= abs(Jurnal::whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['200.210']);})->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit')->sum('jumlah'));

		$nb 				= new NotaBayar;
		$nb->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
		$nb->morph_reference_id 	= $this->kredit['nomor_kredit'];
		$nb->morph_reference_tag 	= 'kredit';
		$nb->tanggal 				= $this->tanggal;
		$nb->karyawan 				= $this->karyawan;
		$nb->jumlah 				= $this->formatMoneyTo($total);
		$nb->jenis 					= 'angsuran';
		$nb->nomor_rekening 		= $this->nomor_perkiraan;
		$nb->save();

		foreach ($nth_akan_d as $k => $v) {
			$pokok 	= $this->formatMoneyFrom($v['pokok']);
			$bunga 	= $this->formatMoneyFrom($v['bunga']);

			if($pokok > 0){
				if($total_titipan > 0){
					$tag 	= 'restitusi_titipan_pokok';
					$total_titipan 	= $total_titipan - $pokok;
				}else{
					$tag 	= 'pokok';
				}
				$deskripsi 	= 'Pokok Angsuran Ke-'.$v['nth'];
				$angs 		= new DetailTransaksi;
				$angs->nomor_faktur 	= $nb->nomor_faktur;
				$angs->tag 				= $tag;
				$angs->jumlah 			= $v['pokok'];
				$angs->deskripsi 		= $deskripsi;
				$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
				$angs->morph_reference_tag 	= 'kredit';
				$angs->save();
			}

			if($bunga > 0){
				if($total_titipan > 0){
					$tag 	= 'restitusi_titipan_bunga';
					$total_titipan 	= $total_titipan - $bunga;
				}else{
					$tag 	= 'bunga';
				}
				$deskripsi 	= 'Bunga Angsuran Ke-'.$v['nth'];
				$angs 		= new DetailTransaksi;
				$angs->nomor_faktur 	= $nb->nomor_faktur;
				$angs->tag 				= $tag;
				$angs->jumlah 			= $v['bunga'];
				$angs->deskripsi 		= $deskripsi;
				$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
				$angs->morph_reference_tag 	= 'kredit';
				$angs->save();
			}

			$v->nomor_faktur 	= $nb->nomor_faktur;
			$v->tanggal_bayar 	= $this->tanggal;
			$v->save();
		}
	}

	public function bayar_sebagian($nominal){
		$nth_belum_bayar 	= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->first();
		$jlh_b_bayar 	= $this->formatMoneyFrom($nth_belum_bayar->jumlah);

		$total_titipan		= abs(Jurnal::whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['200.210']);})->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit')->sum('jumlah'));
		$total_bayar 		= $total_titipan + $this->formatMoneyFrom($nominal);

		if($total_bayar < $jlh_b_bayar){
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

			$deskripsi 	= 'Titipan Pembayaran Tunggakan Angsuran';
			$angs 		= new DetailTransaksi;
			$angs->nomor_faktur 	= $nb->nomor_faktur;
			$angs->tag 				= 'titipan';
			$angs->jumlah 			= $nominal;
			$angs->deskripsi 		= $deskripsi;
			$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$angs->morph_reference_tag 	= 'kredit';
			$angs->save();
		}else{
			throw new AppException(['nominal' => 'Pembayaran Cukup Untuk Melunasi 1 Angsuran'], 1);
		}
	}

	private function hitung_pelunasan($nth_dibayar){
		$today 	= Carbon::createFromFormat('d/m/Y H:i', $this->tanggal);
		//cek semua tunggakan
		$tgk	= JadwalAngsuran::wherenull('nomor_faktur')->where('nomor_kredit', $this->kredit['nomor_kredit'])->where('tanggal', '<', $today->format('Y-m-d H:i:s'))->sum('jumlah');

		if($tgk){
			throw new AppException(['nth' => 'Harap Lunasi Tunggakan Terlebih Dahulu!'], 1);
		}

		//cek titipan
		$ttpn	= DetailTransaksi::whereIn('tag', ['titipan_bunga', 'restitusi_titipan_bunga', 'titipan_pokok', 'restitusi_titipan_pokok'])->wherehas('notabayar', function($q){$q->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit');})->sum('jumlah');

		if($ttpn){
			throw new AppException(['nth' => 'Harap Keluarkan Titipan Terlebih Dahulu!'], 1);
		}

		//hitung bunga
		$pot_b 	= abs($this->formatMoneyFrom(PelunasanAngsuran::potongan($this->kredit['nomor_kredit'])));
		$total 	= 0;

		//pokok
		if(str_is($this->kredit['jenis_pinjaman'], 'pa')){
			$rincian 	= new PerhitunganBunga($this->kredit['plafon_pinjaman'], 'Rp 0', $this->kredit['suku_bunga'], null, null, null, $this->kredit['jangka_waktu']);
			$rincian 	= $rincian->pa();
			
			$angs_p 	= $this->formatMoneyFrom($rincian['angsuran'][$nth_dibayar]['angsuran_pokok']);
			$pokok 		= $angs_p * ($this->kredit['jangka_waktu'] - $nth_dibayar);

			$angs_b 	= $this->formatMoneyFrom($rincian['angsuran'][$nth_dibayar]['angsuran_bunga']);
			$bunga 		= $angs_b * ($this->kredit['jangka_waktu'] - $nth_dibayar);

			$total 		= $pokok + $bunga - $pot_b;
		}elseif(str_is($this->kredit['jenis_pinjaman'], 'pt')){
			$rincian 	= new PerhitunganBunga($this->kredit['plafon_pinjaman'], 'Rp 0', $this->kredit['suku_bunga'], null, null, null, $this->kredit['jangka_waktu']);
			$rincian 	= $rincian->pt();

			$angs_p 	= $this->formatMoneyFrom($rincian['angsuran'][6]['angsuran_pokok']);
			$pokok 		= $angs_p;

			$angs_b 	= $this->formatMoneyFrom($rincian['angsuran'][$nth_dibayar]['angsuran_bunga']);
			$bunga 		= $angs_b * ($this->kredit['jangka_waktu'] - 1 - $nth_dibayar);

			$total 		= $pokok + $bunga - $pot_b;
		}

		//simpan nota bayar
		$nb 				= new NotaBayar;
		$nb->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
		$nb->morph_reference_id 	= $this->kredit['nomor_kredit'];
		$nb->morph_reference_tag 	= 'kredit';
		$nb->tanggal 				= $this->tanggal;
		$nb->karyawan 				= $this->karyawan;
		$nb->jumlah 				= $this->formatMoneyTo($total);
		$nb->jenis 					= 'angsuran';
		$nb->save();

		//simpan pokok
		$deskripsi 	= 'Pelunasan Pokok Angsuran Ke-'.$nth_dibayar.' s/d '.$this->kredit['jangka_waktu'];
		$angs 		= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('deskripsi', $deskripsi)->first();
		if(!$angs){
			$angs 	= new DetailTransaksi;
		}
		$angs->nomor_faktur 	= $nb->nomor_faktur;
		$angs->tag 				= 'pokok';
		$angs->jumlah 			= $this->formatMoneyTo($pokok);
		$angs->deskripsi 		= $deskripsi;
		$angs->save();

		//simpan bunga
		$deskripsi 	= 'Pelunasan Bunga Angsuran Ke-'.$nth_dibayar.' s/d '.$this->kredit['jangka_waktu'];
		$angs 		= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('deskripsi', $deskripsi)->first();
		if(!$angs){
			$angs 	= new DetailTransaksi;
		}
		$angs->nomor_faktur 	= $nb->nomor_faktur;
		$angs->tag 				= 'bunga';
		$angs->jumlah 			= $this->formatMoneyTo($bunga - $pot_b);
		$angs->deskripsi 		= $deskripsi;
		$angs->save();

		$angsurans 		= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->whereIn('nth', $this->nth)->get();

		foreach ($angsurans as $k => $v) {
			$v->nomor_faktur 	= $nb->nomor_faktur;
			$v->save();
		}
	}
}