<?php

namespace App\Service\System;

use Carbon\Carbon;

use Thunderlabid\Kredit\Models\Aktif;
use Thunderlabid\Kredit\Models\JadwalAngsuran;

use App\Service\Traits\IDRTrait;

class PerhitunganBayar
{
	use IDRTrait;

	protected $nomor_kredit;
	protected $nominal;
	protected $jlh_angs;

	/**
	 * Create a new job instance.
	 *
	 * @param  $file
	 * @return void
	 */
	public function __construct($nomor_kredit, Carbon $tanggal, $jlh_angs = 0, $nominal = 'Rp 0')
	{
		$this->nomor_kredit	= $nomor_kredit;
		$this->jlh_angs 	= $jlh_angs;
		$this->nominal 		= $this->formatMoneyFrom($nominal);
		$this->tomorrow		= Carbon::parse($tanggal)->adddays(1);
	}

	public function pa(){
		$bayar 		= [];
		$nominal 	= $this->nominal;
		$titipan	= Calculator::titipanBefore($this->nomor_kredit, $this->tomorrow);

		if($nominal > 0){
			$tb 	= $nominal + $titipan;

			//1a. bayarkan ke piutang

			$tb_1 		= Carbon::parse(JadwalAngsuran::where('tanggal', '<', $this->tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->orderby('tanggal', 'asc')->min('tanggal'));

			$berikut 	= JadwalAngsuran::where('nomor_kredit', $this->nomor_kredit)->wherenull('tanggal_bayar')->where('tanggal', '<=', $tb_1->format('Y-m-d H:i:s'))->selectraw('jumlah as total_j')->selectraw('pokok as total_p')->selectraw('bunga as total_b')->selectraw('nth as nth')->orderby('nth', 'desc')->first();

			$angs_jt= 0;

			while ($tb >= ($angs_jt + $berikut['total_j']) && $tb_1 <= $this->tomorrow){
				$angs_jt 	= $angs_jt + $berikut['total_j'];
				$tb_1 		= $tb_1->addmonthsnooverflow(1);
				$berikut 	= JadwalAngsuran::where('nomor_kredit', $this->nomor_kredit)->wherenull('tanggal_bayar')->where('tanggal', '<=', $tb_1->format('Y-m-d H:i:s'))->selectraw('jumlah as total_j')->selectraw('pokok as total_p')->selectraw('bunga as total_b')->selectraw('nth as nth')->orderby('nth', 'desc')->first();
			}

			if($angs_jt > 0){
				$bayar[]= [
					'tag'		=> 'angsuran_jt',
					'jumlah'	=> $angs_jt
				];
				$tb 	= $tb - $angs_jt;
			}

			if($tb > 0){
				//1b. bayarkan pelunasan
				$pelunasan 	= Calculator::pelunasanBefore($this->nomor_kredit, $this->tomorrow);
				if($tb >= $pelunasan){
					$bayar[]= [
						'tag'		=> 'pelunasan',
						'jumlah'	=> min($tb, $pelunasan)
					];
					$tb 	= $tb - min($tb, $pelunasan);
				}
			}

			if($tb > 0){
				//1d. jika ada sisa
				$bayar[]= [
					'tag'		=> 'angsuran_berikut',
					'jumlah'	=> min($tb, $pelunasan)
				];
			}

		}else{
			$jlh_angs 	= JadwalAngsuran::wherenull('tanggal_bayar')->where('nomor_kredit', $this->nomor_kredit)->count();
			
			if($this->jlh_angs >= $jlh_angs){
				$jlh_piut 	= JadwalAngsuran::where('tanggal', '>=', $this->tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('tanggal_bayar')->count();

				//1a. bayar angsuran pelunasan
				$pelunasan 	= Calculator::pelunasanBefore($this->nomor_kredit, $this->tomorrow);
				$bayar[]= [
					'tag'		=> 'pelunasan',
					'jumlah'	=> $pelunasan
				];
			
				$this->jlh_angs = $this->jlh_angs - $jlh_piut;
			}
			
			$jlh_piut 	= JadwalAngsuran::where('tanggal', '<', $this->tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('tanggal_bayar')->count();

			if($jlh_piut > 0){
				$piut	= JadwalAngsuran::where('tanggal', '<', $this->tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('tanggal_bayar')->skip(0)->take(min($jlh_piut, $this->jlh_angs))->select(['jumlah as total'])->get()->toarray();

				$piut 	= array_sum(array_column($piut, 'total'));

				//1b. bayar angsuran
				$bayar[]= [
					'tag'		=> 'angsuran_jt',
					'jumlah'	=> $piut 
				];

				$this->jlh_angs 	= $this->jlh_angs - min($jlh_piut, $this->jlh_angs);	
			}

			if($this->jlh_angs > 0){
				$titip	= JadwalAngsuran::where('tanggal', '>=', $this->tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('tanggal_bayar')->skip(0)->take($this->jlh_angs)->select(['jumlah as total'])->get()->toarray();

				$titip 	= array_sum(array_column($titip, 'total'));

				$bayar[]= [
					'tag'		=> 'angsuran_berikut',
					'jumlah'	=> $titip  
				];

				// $titipan 	= 0;	
				$jlh_piut 	= 0;	
			}
		}

		if($titipan > 0){
			$bayar[]=[
				'tag'	=> 'titipan_angsuran',
				'jumlah'	=> 0 - abs($titipan)
			];
		}

		return $bayar;
	}

	public function pt(){
		$bayar 		= [];
		$nominal 	= $this->nominal;
		$titipan	= Calculator::titipanBefore($this->nomor_kredit, $this->tomorrow);
		
		if($nominal > 0){
			$tb 	= $nominal + $titipan;

			//1a. bayarkan ke piutang
			$piut	= Calculator::PiutangBefore($this->nomor_kredit, $this->tomorrow);

			$bayar[]= [
				'tag'		=> 'angsuran_jt',
				'jumlah'	=> min($tb, $piut)
			];

			$tb 	= $tb - min($tb, $piut);

			if($tb > 0){
				//1b. bayarkan pelunasan
				$pelunasan 	= Calculator::pelunasanBefore($this->nomor_kredit, $this->tomorrow);
				if($tb >= $pelunasan){
					$bayar[]= [
						'tag'		=> 'pelunasan',
						'jumlah'	=> min($tb, $pelunasan)
					];
					$sisa_l = min($tb, $pelunasan);
					$tb 	= $tb - min($tb, $pelunasan);
				}else{
					$sisa_l = 0;
				}
			}

			if($tb > 0){
				$sisa_h 	= Calculator::hutangBefore($this->nomor_kredit, $this->tomorrow) - $piut - $sisa_l;
				$next_angs 	= JadwalAngsuran::where('tanggal', '>=', $this->tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->selectraw('jumlah as total')->first();
				
				if($sisa_h > 0 && $tb > $next_angs['total']){
					//1d. jika ada dan cukup untuk turun pokok
					$bayar[]= [
						'tag'		=> 'turun_pokok',
						'jumlah'	=> min($tb, $sisa_h)
					];

					$tb 		= $tb - min($tb, $sisa_h);
				}
			}

			if($tb > 0){
				//1d. jika ada dan cukup untuk turun pokok
				$bayar[]= [
					'tag'		=> 'angsuran_berikut',
					'jumlah'	=> $tb
				];
				$tb 	= 0;
			}

			// if($tb > 0){
			// 	//1c. bayarkan ke denda
			// 	$denda	= Calculator::dendaBefore($this->nomor_kredit, $this->tomorrow);
			// 	$bayar[]= [
			// 		'tag'		=> 'denda',
			// 		'jumlah'	=> $denda
			// 	];
			// 	$tb 	= 0;
			// }

		}else{
			$jlh_angs 	= JadwalAngsuran::wherenull('nomor_faktur')->where('nomor_kredit', $this->nomor_kredit)->count();

			if($this->jlh_angs >= $jlh_angs){
				$jlh_piut 	= JadwalAngsuran::where('tanggal', '>=', $this->tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->count();

				//1a. bayar angsuran pelunasan
				$pelunasan 	= Calculator::pelunasanBefore($this->nomor_kredit, $this->tomorrow);
				$bayar[]= [
					'tag'		=> 'pelunasan',
					'jumlah'	=> $pelunasan
				];

				$this->jlh_angs = $this->jlh_angs - $jlh_piut;
			}
			
			$jlh_piut 	= JadwalAngsuran::where('tanggal', '<', $this->tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->count();

			if($jlh_piut > 0){
				$piut	= JadwalAngsuran::where('tanggal', '<', $this->tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->skip(0)->take(min($jlh_piut, $this->jlh_angs))->select(['jumlah as total'])->get()->toarray();

				$piut 	= array_sum(array_column($piut, 'total'));

				//1b. bayar angsuran
				$bayar[]= [
		
					'tag'		=> 'angsuran_jt',
					'jumlah'	=> $piut
				];

				$this->jlh_angs 	= $this->jlh_angs - min($jlh_piut, $this->jlh_angs);	
			}

			if($this->jlh_angs > 0){
				$titip	= JadwalAngsuran::where('tanggal', '>=', $this->tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->skip(0)->take($this->jlh_angs)->select(['jumlah as total'])->get()->toarray();
				$titip 	= array_sum(array_column($titip, 'total'));

				$bayar[]= [
					'tag'		=> 'angsuran_berikut',
					'jumlah'	=> $titip
				];

				$jlh_piut 	= 0;	
			}
		}
		
		if($titipan > 0){
			$bayar[]=[
				'tag'	=> 'titipan_angsuran',
				'jumlah'	=> 0 - abs($titipan)
			];
		}
		
		return $bayar;
	}

	public function generateFaktur($nomor_kredit, array $bayar, Carbon $tanggal){
		
		$tanggal_1 		= Carbon::parse(JadwalAngsuran::where('nomor_kredit', $nomor_kredit)->wherenull('tanggal_bayar')->orderby('tanggal', 'asc')->min('tanggal'))->adddays(1);
		$limit_date 	= Carbon::parse($tanggal)->adddays(1);
		$nth 			= JadwalAngsuran::where('nomor_kredit', $nomor_kredit)->wherenull('tanggal_bayar')->min('nth');
		$total 			= array_sum(array_column($bayar, 'jumlah'));
		$tnth 			= [];
		$nthjt 			= [];
		$th 			= 0;

		$key 			= array_search('titipan_angsuran', array_column($bayar, 'tag'));

		$balance_titipan= 0;
		if($key){
			$titipan 	= abs($bayar[$key]['jumlah']);
			$potongan_t	= abs($bayar[$key]['jumlah']);
		}else{
			$titipan 	= 0;
			$potongan_t	= 0;
		}

		foreach ($bayar as $k => $v) {
			if(str_is($v['tag'], 'angsuran_jt')){
				$th 	= $th + $v['jumlah'];
				$piut 	= 0;
				$prev_p 	= 0;
				$prev_b 	= 0;

				while ($v['jumlah'] > 0) {
					$berikut 	= JadwalAngsuran::where('nomor_kredit', $nomor_kredit)->wherenull('tanggal_bayar')->where('tanggal', '<=', $tanggal_1->format('Y-m-d H:i:s'))->selectraw('jumlah as total_j')->selectraw('pokok as total_p')->selectraw('bunga as total_b')->selectraw('nth as nth')->orderby('nth', 'desc')->first();

					if($berikut && $v['jumlah'] >= $berikut->total_j){
						if($titipan < $berikut->total_j && $titipan > 0){
							$balance_titipan 	= $berikut->total_j - $titipan;
							$titipan 			= 0;
						}else{
							$titipan 			= max(0, $titipan - $berikut->total_j);
						}
						if($berikut->total_p > 0){
							$faktur[] 	= [
								'deskripsi'	=> 'Pembayaran Pokok Angsuran Ke-'.$berikut->nth,
								'tag'		=> 'pokok',
								'jumlah'	=> self::formatMoneyTo($berikut->total_p),
							];
							$v['jumlah'] 	= $v['jumlah'] - $berikut->total_p;
						}

						if($berikut->total_b > 0){
							$faktur[] 	= [
								'deskripsi'	=> 'Pembayaran Bunga Angsuran Ke-'.$berikut->nth,
								'tag'		=> 'bunga',
								'jumlah'	=> self::formatMoneyTo($berikut->total_b),
							];
							$v['jumlah'] 	= $v['jumlah'] - $berikut->total_b;
						}

						$tnth[]		= $berikut->nth;
						$nthjt[]	= $berikut->nth;
						$nth 		= $nth +1;
						$tanggal_1 	= $tanggal_1->addmonthsnooverflow(1);
					}else{
						$faktur[] 	= [
								'deskripsi'	=> 'Titipan Angsuran',
								'tag'		=> 'titipan',
								'jumlah'	=> self::formatMoneyTo($v['jumlah']),
							];
						$v['jumlah']= 0;
					}
				}
			}elseif(str_is($v['tag'], 'angsuran_berikut')){
				$tanggal_2 		= Carbon::parse($tanggal);
				//sampai jumlah habis
				while ($v['jumlah'] > 0) {
					$berikut 	= JadwalAngsuran::where('nomor_kredit', $nomor_kredit)->wherenull('tanggal_bayar')->where('tanggal', '>', $tanggal_2->format('Y-m-d H:i:s'))->selectraw('jumlah as total_j')->selectraw('pokok as total_p')->selectraw('bunga as total_b')->selectraw('nth as nth')->first();

					if($berikut && $v['jumlah'] >= $berikut->total_j){
						if($titipan < $berikut->total_j && $titipan > 0){
							$balance_titipan 	= $berikut->total_j - $titipan;
							$titipan 			= 0;
						}else{
							$titipan 			= max(0, $titipan - $berikut->total_j);
						}

						if($berikut->total_p > 0){
							$faktur[] 	= [
								'deskripsi'	=> 'Pembayaran Pokok Angsuran Ke-'.$berikut->nth,
								'tag'		=> 'titipan',
								'jumlah'	=> self::formatMoneyTo($berikut->total_p),
							];
							$v['jumlah'] 	= $v['jumlah'] - $berikut->total_p;
							$th 			= $th + $berikut->total_p;
						}

						if($berikut->total_b > 0){
							$faktur[] 	= [
								'deskripsi'	=> 'Pembayaran Bunga Angsuran Ke-'.$berikut->nth,
								'tag'		=> 'titipan',
								'jumlah'	=> self::formatMoneyTo($berikut->total_b),
							];
							$v['jumlah'] 	= $v['jumlah'] - $berikut->total_b;
						}

						$tnth[]		= $berikut->nth;
						$nth 		= $nth +1;
						$tanggal_2 	= $tanggal_2->addmonthsnooverflow(1);

					}else{
						$faktur[] 	= [
								'deskripsi'	=> 'Titipan Angsuran',
								'tag'		=> 'titipan',
								'jumlah'	=> self::formatMoneyTo($v['jumlah']),
							];
						$v['jumlah'] 	= 0;
					}
				}

			}
			elseif(str_is($v['tag'], 'pelunasan')){
				$sisa_b 		= Calculator::pelunasanBungaBefore($nomor_kredit, $limit_date);
				$sisa_p 		= $v['jumlah'] - $sisa_b;

				$nths 			= JadwalAngsuran::where('nomor_kredit', $nomor_kredit)->where('tanggal', '>=', $limit_date->format('Y-m-d H:i:s'))->get(['nth']);

				foreach ($nths as $kjw => $jw) {
					$tnth[]		= $jw['nth'];
				}

				if($sisa_p > 0){
					$faktur[] 	= [
						'deskripsi'	=> 'Pelunasan Pokok Angsuran',
						'tag'		=> 'pokok',
						'jumlah'	=> self::formatMoneyTo($sisa_p),
					];

					$th 		= $th + $sisa_p;
				}

				if($sisa_b > 0){
					$faktur[] 	= [
						'deskripsi'	=> 'Pelunasan Bunga Angsuran',
						'tag'		=> 'bunga',
						'jumlah'	=> self::formatMoneyTo($sisa_b),
					];
				}
			}elseif(str_is($v['tag'], 'turun_pokok')){
				$faktur[] 	= [
					'deskripsi'	=> 'Turun Pokok Angsuran',
					'tag'		=> 'pokok',
					'jumlah'	=> self::formatMoneyTo($v['jumlah']),
				];
			}
		}

		sort($tnth);

		return ['isi' => $faktur, 'total' => $total, 'nth_jt' => $nthjt, 'nth' => $tnth, 'bayar_hutang' => $th, 'balance_titipan' => $balance_titipan, 'potongan_titipan' => $potongan_t];
	}
}