<?php

namespace App\Http\Service\Policy;

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
		$nominal 	= $this->nominal;
		$titipan	= Calculator::titipanBefore($this->nomor_kredit, $this->tomorrow);
		if($nominal > 0){
			$tb 	= $nominal + $titipan;

			//1a. bayarkan ke piutang
			$piut	= Calculator::PiutangBefore($this->nomor_kredit, $this->tomorrow);
			$bayar[]= [
				'tag'		=> 'piutang',
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
					$tb 	= $tb - min($tb, $pelunasan);
				}
			}

			if($tb > 0){
				//1c. bayarkan ke denda
				$denda	= Calculator::dendaBefore($this->nomor_kredit, $this->tomorrow);
				$bayar[]= [
					'tag'		=> 'denda',
					'jumlah'	=> min($tb, $denda)
				];
				$tb 	= $tb - min($tb, $denda);
			}


			if($tb > 0){
				//1d. jika ada sisa
				$bayar[]= [
					'tag'		=> 'titipan',
					'jumlah'	=> min($tb, $pelunasan)
				];
			}

		}else{
			$jlh_angs 	= JadwalAngsuran::wherenull('nomor_faktur')->where('nomor_kredit', $this->nomor_kredit)->count();
			
			if($jlh_angs >= $this->jlh_angs){
				//1a. bayar angsuran pelunasan
				$pelunasan 	= Calculator::pelunasanBefore($this->nomor_kredit, $this->tomorrow);
				$bayar[]= [
					'tag'		=> 'pelunasan',
					'jumlah'	=> $pelunasan
				];
			}
			
			$jlh_piut 	= JadwalAngsuran::where('tanggal', '<', $tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->count();

			if($jlh_piut > 0){
				$piut	= JadwalAngsuran::where('tanggal', '<', $tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->skip(0)->take(min($jlh_piut, $this->jlh_angs))->sum('jumlah');

				//1b. bayar angsuran
				$bayar[]= [
					'tag'		=> 'piutang',
					'jumlah'	=> $piut;  
				];

				$jlh_piut 	= min($jlh_piut, $this->jlh_angs);	
			}

			if($jlh_piut > 0){
				$titip	= JadwalAngsuran::where('tanggal', '>=', $tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->skip(0)->take($jlh_piut)->sum('jumlah');

				$bayar[]= [
					'tag'		=> 'titipan',
					'jumlah'	=> $titip;  
				];

				$jlh_piut 	= 0;	
			}
		}
	}

	public function pt(){
		$nominal 	= $this->nominal;
		$titipan	= Calculator::titipanBefore($this->nomor_kredit, $this->tomorrow);
		if($nominal > 0){
			$tb 	= $nominal + $titipan;

			//1a. bayarkan ke piutang
			$piut	= Calculator::PiutangBefore($this->nomor_kredit, $this->tomorrow);
			$bayar[]= [
				'tag'		=> 'piutang',
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
					$tb 	= $tb - min($tb, $pelunasan);
				}
			}

			if($tb > 0){
				//1c. bayarkan ke denda
				$denda	= Calculator::dendaBefore($this->nomor_kredit, $this->tomorrow);
				$bayar[]= [
					'tag'		=> 'denda',
					'jumlah'	=> min($tb, $denda)
				];
				$tb 	= $tb - min($tb, $denda);
			}


			if($tb > 0){
				$sisa_h 	= Calculator::hutangBefore($this->nomor_kredit, $this->tomorrow) - $piut;

				//1d. jika ada dan cukup untuk turun pokok
				$bayar[]= [
					'tag'		=> 'turun_pokok',
					'jumlah'	=> min($tb, $sisa_h)
				];

				$tb 		= $tb - min($tb, $sisa_h);
			}

			if($tb > 0){
				//1d. jika ada dan cukup untuk turun pokok
				$bayar[]= [
					'tag'		=> 'turun_pokok',
					'jumlah'	=> $tb
				];
				$tb 	= 0;
			}

		}else{
			$jlh_angs 	= JadwalAngsuran::wherenull('nomor_faktur')->where('nomor_kredit', $this->nomor_kredit)->count();
			
			if($jlh_angs >= $this->jlh_angs){
				//1a. bayar angsuran pelunasan
				$pelunasan 	= Calculator::pelunasanBefore($this->nomor_kredit, $this->tomorrow);
				$bayar[]= [
					'tag'		=> 'pelunasan',
					'jumlah'	=> $pelunasan
				];
			}
			
			$jlh_piut 	= JadwalAngsuran::where('tanggal', '<', $tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->count();

			if($jlh_piut > 0){
				$piut	= JadwalAngsuran::where('tanggal', '<', $tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->skip(0)->take(min($jlh_piut, $this->jlh_angs))->sum('jumlah');

				//1b. bayar angsuran
				$bayar[]= [
					'tag'		=> 'piutang',
					'jumlah'	=> $piut;  
				];

				$jlh_piut 	= min($jlh_piut, $this->jlh_angs);	
			}

			if($jlh_piut > 0){
				$titip	= JadwalAngsuran::where('tanggal', '>=', $tomorrow->format('Y-m-d H:i:s'))->where('nomor_kredit', $this->nomor_kredit)->wherenull('nomor_faktur')->skip(0)->take($jlh_piut)->sum('jumlah');

				$bayar[]= [
					'tag'		=> 'titipan',
					'jumlah'	=> $titip;  
				];

				$jlh_piut 	= 0;	
			}
		}
	}
}