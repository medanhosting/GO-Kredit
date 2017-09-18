<?php

namespace App\Listeners;

///////////////
// Exception //
///////////////
use Thunderlabid\Survei\Exceptions\AppException;

///////////////
// Framework //
///////////////
use Carbon\Carbon, Auth;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use Thunderlabid\Pengajuan\Models\LegalRealisasi;
use Thunderlabid\Pengajuan\Models\Analisa;
use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Survei\Models\Survei;

class GenerateLegalitasRealisasi
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle event
	 * @param  KantorCreated $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model = $event->data;

		if($model->putusan=='setuju')
		{
			foreach ($model->checklists['pengikat'] as $k => $v) 
			{
				$data['jenis']			= $k;
				$data['pengajuan_id']	= $model->pengajuan['id'];

				switch ($k) {
					case 'permohonan_kredit':
						$data['isi']	= Pengajuan::where('id', $model->pengajuan_id)->orderby('created_at', 'desc')->with(['jaminan'])->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'survei_report':
						$data['isi']	= Survei::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->with(['character', 'condition', 'capital', 'capacity', 'collateral', 'pengajuan', 'foto'])->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'persetujuan_komite':
						$data['isi']	= Putusan::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'pernyataan_analis':
						$data['isi']	= Analisa::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'perjanjian_kredit':
						$data['isi']		= Analisa::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->first();
						if(strtolower($data['isi']['jenis_pinjaman'])=='pa')
						{
							$data['jenis']	= $k.'_angsuran';
						}
						else
						{
							$data['jenis']	= $k.'_musiman';
						}
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'pengakuan_hutang':
						$data['isi']	= Putusan::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'penggantian_jaminan':
						$data['isi']	= Putusan::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'skmht_apht':
						$data['isi']	= Survei::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->with(['collateral'])->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'feo':
						$data['isi']	= Survei::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->with(['collateral'])->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'surat_persetujuan_keluarga':
						$data['isi']	= Pengajuan::where('id', $model->pengajuan_id)->orderby('created_at', 'desc')->with(['jaminan'])->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'surat_persetujuan_plang':
						$data['isi']	= Putusan::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'pernyataan_belum_balik_nama':
						$data['isi']	= Survei::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->with(['collateral'])->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'kuasa_pembebanan_feo':
						$data['isi']	= Survei::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->with(['collateral'])->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
					case 'kuasa_menjual_dan_menarik_jaminan':
						$data['isi']	= Survei::where('pengajuan_id', $model->pengajuan_id)->orderby('tanggal', 'desc')->with(['collateral'])->first()->toArray();
						$data['nomor']	= $model->pengajuan['id'];
						break;
				}

				$legal 		= LegalRealisasi::where('pengajuan_id', $model->pengajuan_id)->where('jenis', $data['jenis'])->first();
				if(!$legal)
				{
					$legal 	= new LegalRealisasi;
				}

				$legal->fill($data);
				$legal->save();
			}
		}
	}
}
