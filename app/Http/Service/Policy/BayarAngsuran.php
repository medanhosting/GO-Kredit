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
		$today 		= Carbon::createFromFormat('d/m/Y H:i', $this->tanggal);
		$tomorrow 	= Carbon::createFromFormat('d/m/Y H:i', $this->tanggal)->adddays(1);
		
		//1. check titipan
		$titipan	= Calculator::titipanBefore($this->kredit['nomor_kredit'], $tomorrow);
		$piut		= Calculator::piutangBefore($this->kredit['nomor_kredit'], $tomorrow);
		$nth_akan_d = JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->skip(0)->take($this->nth)->orderby('nth', 'asc')->get();
		$total_bayar 	= JadwalAngsuran::whereIn('id', array_column($nth_akan_d->toarray(), 'id'))->sum('jumlah');

		$is_t_lunas 	= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->skip($this->nth)->take(1)->first();

		$potongan 		= 0;
		if(!$is_t_lunas){
			$potongan 	= Calculator::potonganBefore($this->kredit['nomor_kredit'], $tomorrow);
		}

		if($piut <= 0 && $is_t_lunas){
			//simpan tanda terima
			$nb 				= new NotaBayar;
			$nb->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
			$nb->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$nb->morph_reference_tag 	= 'kredit';
			$nb->tanggal 				= $this->tanggal;
			$nb->karyawan 				= $this->karyawan;
			$nb->jumlah 				= $this->formatMoneyTo($total_bayar);
			$nb->jenis 					= 'angsuran_sementara';
			$nb->save();

			$deskripsi 	= 'Titipan Pembayaran Angsuran';
			$angs 		= new DetailTransaksi;
			$angs->nomor_faktur 	= $nb->nomor_faktur;
			$angs->tag 				= 'titipan';
			$angs->jumlah 			= $this->formatMoneyTo($total_bayar);
			$angs->deskripsi 		= $deskripsi;
			$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$angs->morph_reference_tag 	= 'kredit';
			$angs->save();

			return true;
		}

		//balance titipan
		if($titipan > 0){
			$satu_angs 	= $this->formatMoneyFrom($nth_akan_d[0]['jumlah']);

			if($titipan%$satu_angs!=0 && $titipan < $satu_angs){
				//simpan balance angsuran
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
				$angs->jumlah 			= $this->formatMoneyTo($satu_angs - ($titipan % $satu_angs));
				$angs->deskripsi 		= $deskripsi;
				$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
				$angs->morph_reference_tag 	= 'kredit';
				$angs->save();
			}
		}

		//simpan tanda terima
		$nb 				= new NotaBayar;
		$nb->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
		$nb->morph_reference_id 	= $this->kredit['nomor_kredit'];
		$nb->morph_reference_tag 	= 'kredit';
		$nb->tanggal 				= $this->tanggal;
		$nb->karyawan 				= $this->karyawan;
		$nb->nomor_rekening 		= $this->nomor_perkiraan;
		$nb->jumlah 				= $this->formatMoneyTo($total_bayar-$potongan-$titipan);
		$nb->jenis 					= 'angsuran';
		$nb->save();

		$ptjt 		= 0;
		$btjt 		= 0;
		
		foreach ($nth_akan_d as $k => $v) {
			//jika jatuh tempo bayar, jika tidak sum
			$tgl 	= Carbon::createFromFormat('d/m/Y H:i', $v['tanggal']);

			if($today->startofday()->format('Y-m-d H:i:s') > $tgl->startofday()->format('Y-m-d H:i:s')){
				//bayar bunga dan pokok
				if($v['pokok'] != 'Rp 0'){
					$deskripsi 	= 'Pokok Angsuran';
					$angs 		= new DetailTransaksi;
					$angs->nomor_faktur 	= $nb->nomor_faktur;
					$angs->tag 				= 'pokok';
					$angs->jumlah 			= $v['pokok'];
					$angs->deskripsi 		= $deskripsi;
					$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
					$angs->morph_reference_tag 	= 'kredit';
					$angs->save();
				}

				if($v['bunga'] != 'Rp 0'){
					$deskripsi 	= 'Bunga Angsuran';
					$angs 		= new DetailTransaksi;
					$angs->nomor_faktur 	= $nb->nomor_faktur;
					$angs->tag 				= 'bunga';
					$angs->jumlah 			= $v['bunga'];
					$angs->deskripsi 		= $deskripsi;
					$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
					$angs->morph_reference_tag 	= 'kredit';
					$angs->save();
				}

			}else{
				$ptjt 	= $ptjt + $this->formatMoneyFrom($v['pokok']);
				$btjt 	= $btjt + $this->formatMoneyFrom($v['bunga']);
			}
		}

		if($ptjt > 0){
			$deskripsi 	= 'Pokok Angsuran';
			$angs 		= new DetailTransaksi;
			$angs->nomor_faktur 	= $nb->nomor_faktur;
			$angs->tag 				= 'pokok';
			$angs->jumlah 			= $this->formatMoneyTo($ptjt);
			$angs->deskripsi 		= $deskripsi;
			$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$angs->morph_reference_tag 	= 'kredit';
			$angs->save();
		}

		if($btjt > 0){
			$deskripsi 	= 'Bunga Angsuran';
			$angs 		= new DetailTransaksi;
			$angs->nomor_faktur 	= $nb->nomor_faktur;
			$angs->tag 				= 'bunga';
			$angs->jumlah 			= $this->formatMoneyTo($btjt - $potongan);
			$angs->deskripsi 		= $deskripsi;
			$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
			$angs->morph_reference_tag 	= 'kredit';
			$angs->save();
		}

		$nth_akan_d = JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->skip(0)->take($this->nth)->update(['nomor_faktur' => $nb->nomor_faktur, 'tanggal_bayar' => $this->formatDateTimeFrom($this->tanggal)]);

		return true;
	}

	public function bayar_sebagian($nominal){
		$tunggakan 	= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->orderby('nth', 'asc')->first();
		$jumlah_t 	= $this->formatMoneyFrom($tunggakan->jumlah);

		$titipan	= Calculator::titipanBefore($this->kredit['nomor_kredit'], Carbon::createFromFormat('d/m/Y H:i', $this->tanggal));

		$piut		= Calculator::piutangBefore($this->kredit['nomor_kredit'], Carbon::createFromFormat('d/m/Y H:i', $this->tanggal));

		$bayar 		= $titipan + $this->formatMoneyFrom($nominal);

		if($bayar <= $jumlah_t || $piut == 0){
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

	public function penerimaan_kas_kolektor($nomor_faktur){
		$tagih 				= Penagihan::where('nomor_faktur', $nomor_faktur)->first();
		if($tagih){
			$tagih->tag 	= "completed";
			$tagih->save();
		}

		$tanggal 	= Carbon::createFromFormat('d/m/Y H:i', $this->tanggal);

		//nominal
		$tnb	= NotaBayar::where('nomor_faktur', $nomor_faktur)->sum('jumlah');
		$ttpn	= Calculator::titipanBefore($this->kredit['nomor_kredit'], $tanggal->adddays(1));
		$tnb 	= $tnb + $ttpn;
		//simpan nb baru
		$nb 				= new NotaBayar;
		$nb->nomor_faktur	= NotaBayar::generatenomorfaktur($this->kredit['nomor_kredit']);
		$nb->morph_reference_id 	= $nomor_faktur;
		$nb->morph_reference_tag 	= 'finance';
		$nb->nomor_rekening 		= $this->nomor_perkiraan;
		$nb->tanggal 				= $this->tanggal;
		$nb->karyawan 				= $this->karyawan;
		$nb->jumlah 				= $this->formatMoneyTo($tnb);
		$nb->jenis 					= 'memorial_kolektor';
		$nb->save();

		while ($tnb > 0) {
			$angs_bdb 	= JadwalAngsuran::where('nomor_kredit', $this->kredit['nomor_kredit'])->wherenull('nomor_faktur')->where('tanggal', '<', $tanggal->format('Y-m-d H:i:s'))->orderby('nth', 'asc')->orderby('nth', 'asc')->selectraw('*')->selectraw('jumlah as jlh_t')->selectraw('pokok as jlh_p')->selectraw('bunga as jlh_b')->first();

			if($angs_bdb && $tnb >= $angs_bdb['jlh_t']){
				if($angs_bdb['jlh_p'] > 0){
					$deskripsi 	= 'Pokok Angsuran';
					$angs 		= new DetailTransaksi;
					$angs->nomor_faktur 	= $nb->nomor_faktur;
					$angs->tag 				= 'pokok';
					$angs->jumlah 			= $angs_bdb['pokok'];
					$angs->deskripsi 		= $deskripsi;
					$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
					$angs->morph_reference_tag 	= 'kredit';
					$angs->save();
				}

				if($angs_bdb['jlh_b'] > 0){
					$deskripsi 	= 'Bunga Angsuran';
					$angs 		= new DetailTransaksi;
					$angs->nomor_faktur 	= $nb->nomor_faktur;
					$angs->tag 				= 'bunga';
					$angs->jumlah 			= $angs_bdb['bunga'];
					$angs->deskripsi 		= $deskripsi;
					$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
					$angs->morph_reference_tag 	= 'kredit';
					$angs->save();
				}

				$up_angs 	= JadwalAngsuran::where('id', $angs_bdb['id'])->update(['nomor_faktur' => $nb->nomor_faktur, 'tanggal_bayar' => $tanggal->format('Y-m-d H:i:s')]);

				$tnb 	= $tnb - $angs_bdb['jlh_t'];
			}else{
				$deskripsi 	= 'Titipan Angsuran';
				$angs 		= new DetailTransaksi;
				$angs->nomor_faktur 	= $nb->nomor_faktur;
				$angs->tag 				= 'titipan';
				$angs->jumlah 			= $this->formatMoneyTo($tnb - $ttpn);
				$angs->deskripsi 		= $deskripsi;
				$angs->morph_reference_id 	= $this->kredit['nomor_kredit'];
				$angs->morph_reference_tag 	= 'kredit';
				$angs->save();

				$tnb 	= 0;
			}
		}
	}
}