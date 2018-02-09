<?php

namespace Thunderlabid\Kredit\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

use Thunderlabid\Kredit\Models\AngsuranDetail;
use Thunderlabid\Kredit\Models\MutasiJaminan;

use Carbon\Carbon;

class TandaiJaminanKeluar
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
	 * @param  PenagihanCreated $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model	= $event->data;

		if(!is_null($model->nota_bayar_id)){
			//check titipan
			$titipan		= abs(Jurnal::whereHas('coa', function($q){$q->whereIn('nomor_perkiraan', ['200.210']);})->where('morph_reference_id', $aktif['nomor_kredit'])->where('morph_reference_tag', 'kredit')->sum('jumlah'));
			
			//check angsuran
			$not_yet_paid 	= JadwalAngsuran::wherenull('nomor_faktur')->where('nomor_kredit', $model->nomor_kredit)->count();

			//check denda
			$rd			= JadwalAngsuran::historiTunggakan($today)->where('nomor_kredit', $aktif['nomor_kredit'])->groupby('nth')->selectraw('sum(jumlah * ('.$aktif['persentasi_denda'].'/100)) as tunggakan')->selectraw('DATEDIFF(min(tanggal),IFNULL(min(tanggal_bayar),"'.$today->format('Y-m-d H:i:s').'")) as days')->selectraw('nth')->get();

			$denda 		= 0;
			foreach ($rd as $k => $v) {
				$denda 	= $denda + ($v['tunggakan'] * abs($v['days'])); 
			}
			$denda		= ceil($denda) - ceil(NotaBayar::where('morph_reference_id', $aktif['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('jenis', 'denda')->sum('jumlah')) - ceil(PermintaanRestitusi::where('is_approved', true)->where('nomor_kredit', $aktif['nomor_kredit'])->sum('jumlah'));


			if(!$not_yet_paid && !$titipan && !$denda){
				$ids 		= MutasiJaminan::where('nomor_kredit', $model['nomor_kredit'])->selectraw('max(id) as id, max(tanggal) as tanggal')->groupby('nomor_jaminan')->orderby('tanggal', 'desc')->get()->toArray();

				$jaminan 	= MutasiJaminan::wherein('id', array_column($ids, 'id'))->get();
				foreach ($jaminan as $k => $v) {
					$m_jaminan 					= new MutasiJaminan;
					$m_jaminan->nomor_kredit 	= $v->nomor_kredit;
					$m_jaminan->nomor_jaminan 	= $v->nomor_jaminan;
					$m_jaminan->kategori 		= $v->kategori;
					$m_jaminan->tanggal 		= Carbon::now()->format('d/m/Y H:i');
					$m_jaminan->tag 			= $v->tag;
					$m_jaminan->status 			= 'titipan';
					$m_jaminan->deskripsi 		= 'Angsuran Lunas';
					$m_jaminan->dokumen 		= $v->dokumen;
					$m_jaminan->karyawan 		= $v->karyawan;
					$m_jaminan->save();
				}
			}
		}
	}
}