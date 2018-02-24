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
	public function __construct($nomor_kredit, $tanggal, $jlh_angs = 0, $nominal = 'Rp 0')
	{
		$this->nomor_kredit	= $nomor_kredit;
		$this->jlh_angs 	= $jlh_angs;
		$this->nominal 		= $this->formatMoneyFrom($nominal);
		$this->tomorrow		= Carbon::createfromformat('d/m/Y', $tanggal)->adddays(1);
	}

	public function pa(){
		$bayar 		= [];
		$nominal 	= $this->nominal;
		$titipan	= Calculator::titipanBefore($this->nomor_kredit, $this->tomorrow);
		
		if($nominal > 0){
			$tb 	= $nominal + $titipan;

			//1a. bayarkan ke piutang

			$tb_1 	= Carbon::parse(JadwalAngsuran::where('tanggal', '<', $this->tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->orderby('tanggal', 'asc')->min('tanggal'));

			$piut	= Calculator::PiutangBefore($this->nomor_kredit, $tb_1->adddays(1));
			$angs_jt= 0;

			while ($piut > 0 && $tb > $piut){
				$angs_jt 	= $piut;
				$piut		= Calculator::PiutangBefore($this->nomor_kredit, $tb_1->addmonthsnooverflow(1));
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

			// if($tb > 0){
			// 	//1c. bayarkan ke denda
			// 	$denda	= Calculator::dendaBefore($this->nomor_kredit, $this->tomorrow);
			// 	$bayar[]= [
			// 		'tag'		=> 'denda',
			// 		'jumlah'	=> min($tb, $denda)
			// 	];
			// 	$tb 	= $tb - min($tb, $denda);
			// }


			if($tb > 0){
				//1d. jika ada sisa
				$bayar[]= [
					'tag'		=> 'angsuran_berikut',
					'jumlah'	=> min($tb, $pelunasan)
				];
			}

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
}