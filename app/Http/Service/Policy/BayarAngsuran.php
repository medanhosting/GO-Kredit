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
		
		$nth_akan_d 	= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->whereNotIn('nth', $this->nth)->count();

		if(!$nth_akan_d){
			return $this->hitung_pelunasan($nth_dibayar);
		}

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

		$titipan	= DetailTransaksi::whereIn('tag', ['titipan_bunga', 'restitusi_titipan_bunga', 'titipan_pokok', 'restitusi_titipan_pokok'])->wherehas('notabayar', function($q){$q->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit');})->sum('jumlah');

		//buat bukti memorial
		if($titipan > 0){
			$bm 				= new NotaBayar;
			$bm->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
			$bm->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$bm->morph_reference_tag 	= 'kredit';
			$bm->tanggal 				= $this->tanggal;
			$bm->karyawan 				= $this->karyawan;
			$bm->jumlah 				= $this->formatMoneyTo($titipan);
			$bm->jenis 					= 'memorial_titipan';
			$bm->save();

			$total_ttp 	= 0;
		}

		//simpan detail
		foreach ($angsurans as $k => $v) {
			//BAYAR DENGAN TITIPAN
			$titipan_b	= DetailTransaksi::whereIn('tag', ['titipan_bunga', 'restitusi_titipan_bunga'])->wherehas('notabayar', function($q){$q->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit');})->sum('jumlah');
			$titipan_p	= DetailTransaksi::whereIn('tag', ['titipan_pokok', 'restitusi_titipan_pokok'])->wherehas('notabayar', function($q){$q->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit');})->sum('jumlah');

			if($rincian['angsuran'][$v['nth']]['angsuran_bunga']!='Rp 0'){
				//check titipan
				$hutang 	= $this->formatMoneyFrom($rincian['angsuran'][$v['nth']]['angsuran_bunga']);

				if($titipan_b > 0){
					//kalau titipan bunga tidak di nolkan, simpan bukti titipan bunga baru
					if($titipan_b%$hutang!=0 && $titipan_b < $hutang){
						//simpan titipan dlu
						$sisa_ttpn 	= $hutang - $titipan_b%$hutang;
						$deskripsi 	= 'Titipan Bunga Angsuran Ke-'.$v['nth'];
						$angs 		= DetailTransaksi::where('nomor_faktur', $bm->nomor_faktur)->where('deskripsi', $deskripsi)->first();
						if(!$angs){
							$angs 	= new DetailTransaksi;
						}
						$angs->nomor_faktur 	= $bm->nomor_faktur;
						$angs->tag 				= 'titipan_bunga';
						$angs->jumlah 			= $this->formatMoneyTo($sisa_ttpn);
						$angs->deskripsi 		= $deskripsi;
						$angs->save();
						$total_ttp 	= $total_ttp + abs($sisa_ttpn);
					}

					$deskripsi 	= 'Restitusi Titipan Bunga Angsuran Ke-'.$v['nth'];
					$angs 		= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('deskripsi', $deskripsi)->first();
					if(!$angs){
						$angs 	= new DetailTransaksi;
					}
					$angs->nomor_faktur 	= $bm->nomor_faktur;
					$angs->tag 				= 'restitusi_titipan_bunga';
					$angs->jumlah 			= $this->formatMoneyTo(0 - abs($hutang));
					$angs->deskripsi 		= $deskripsi;
					$angs->save();

				}
				
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

			if($rincian['angsuran'][$v['nth']]['angsuran_pokok']!='Rp 0'){

				$hutang 	= $this->formatMoneyFrom($rincian['angsuran'][$v['nth']]['angsuran_pokok']);

				if($titipan_p > 0){
					//kalau titipan pokok tidak di nolkan, simpan bukti titipan pokok baru
					if($titipan_p%$hutang!=0 && $titipan_p < $hutang){
						//simpan titipan dlu
						$sisa_ttpn 	= $hutang - $titipan_p%$hutang;
						$deskripsi 	= 'Titipan Pokok Angsuran Ke-'.$v['nth'];
						$angs 		= DetailTransaksi::where('nomor_faktur', $bm->nomor_faktur)->where('deskripsi', $deskripsi)->first();
						if(!$angs){
							$angs 	= new DetailTransaksi;
						}
						$angs->nomor_faktur 	= $bm->nomor_faktur;
						$angs->tag 				= 'titipan_pokok';
						$angs->jumlah 			= $this->formatMoneyTo($sisa_ttpn);
						$angs->deskripsi 		= $deskripsi;
						$angs->save();
						$total_ttp 	= $total_ttp + abs($sisa_ttpn);
					}

					$deskripsi 	= 'Restitusi Titipan Pokok Angsuran Ke-'.$v['nth'];
					$angs 		= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('deskripsi', $deskripsi)->first();
					if(!$angs){
						$angs 	= new DetailTransaksi;
					}
					$angs->nomor_faktur 	= $bm->nomor_faktur;
					$angs->tag 				= 'restitusi_titipan_pokok';
					$angs->jumlah 			= $this->formatMoneyTo(0 - abs($hutang));
					$angs->deskripsi 		= $deskripsi;
					$angs->save();

				}

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
			
			//update nomor faktur
			$v->nomor_faktur 	= $nb->nomor_faktur;
			$v->save();
		}

		if(isset($bm)){
			$bm->jumlah	= $this->formatMoneyTo($total_ttp);
			$bm->save();
		}
	}

	public function bayar_sebagian($nominal){
		$nth_belum_bayar 	= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->first();
		$jlh_b_bayar 	= $this->formatMoneyFrom($nth_belum_bayar->jumlah);

		$kas_titipan		= DetailTransaksi::whereIn('tag', ['titipan_pokok', 'restitusi_titipan_pokok', 'titipan_bunga', 'restitusi_titipan_bunga'])->wherehas('notabayar', function($q){$q->where('morph_reference_id', $this->kredit['nomor_kredit'])->where('morph_reference_tag', 'kredit');})->sum('jumlah');

		$titipan 		= $this->formatMoneyFrom($nominal);
		$total_titipan 	= $kas_titipan + $titipan;

		if($total_titipan <= $jlh_b_bayar){
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

			//pembagian detail untuk PA
			if(str_is($this->kredit['jenis_pinjaman'], 'pa'))
			{
				$rincian 	= new PerhitunganBunga($this->kredit['plafon_pinjaman'], 'Rp 0', $this->kredit['suku_bunga'], null, null, null, $this->kredit['jangka_waktu']);
				$rincian 	= $rincian->pa();
				$angs_b 	= $this->formatMoneyFrom($rincian['angsuran'][$nth_belum_bayar['nth']]['angsuran_bunga']);
				$angs_p 	= $this->formatMoneyFrom($rincian['angsuran'][$nth_belum_bayar['nth']]['angsuran_pokok']);

				if($kas_titipan < $angs_b){
					//sisa hutang bunga
					$sisa_h_b 	= $angs_b - $kas_titipan;
					$deskripsi 	= 'Titipan Angsuran Bunga Ke-'.$nth_belum_bayar['nth'];
					$angs 		= new DetailTransaksi;
					$angs->nomor_faktur 	= $nb->nomor_faktur;
					$angs->tag 				= 'titipan_bunga';
					$angs->jumlah 			= $this->formatMoneyTo(min($titipan, $sisa_h_b));
					$angs->deskripsi 		= $deskripsi;
					$angs->save();
					$titipan		= $titipan - min($titipan, $sisa_h_b);
					$kas_titipan 	= 0;
				}else{
					$kas_titipan 	= $kas_titipan - $angs_b;
				}

				if($titipan > 0){
					//simpan detail
					$sisa_h_p 	= $angs_p - $kas_titipan;
					$deskripsi 	= 'Titipan Angsuran Pokok Ke-'.$nth_belum_bayar['nth'];
					$angs 		= new DetailTransaksi;
					$angs->nomor_faktur 	= $nb->nomor_faktur;
					$angs->tag 				= 'titipan_pokok';
					$angs->jumlah 			= $this->formatMoneyTo(min($titipan, ($sisa_h_p)));
					$angs->deskripsi 		= $deskripsi;
					$angs->save();
					$kas_titipan 	= 0;
				}else{
					$kas_titipan 	= $kas_titipan - $angs_p;
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

	private function hitung_pelunasan($nth_dibayar){
		$today 	= Carbon::now();
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