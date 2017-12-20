<?php

namespace App\Http\Controllers\V2\Traits;

use Thunderlabid\Pengajuan\Models\Pengajuan;
use DB;

/**
 * Trait tanggal
 *
 * Digunakan untuk reformat tanggal sesuai kontrak
 *
 * @package    Thunderlabid
 * @subpackage Credit
 * @author     C Mooy <chelsy@thunderlab.od>
 */
trait PengajuanTrait {
 	
	private function get_pengajuan($status, $ids = null){

		$result		= Pengajuan::status($status)->kantor(request()->get('kantor_aktif_id'))->with(['status_terakhir', 'jaminan_kendaraan', 'jaminan_tanah_bangunan', 'status_permohonan']);
		
		if($ids){
			$result = $result->whereIn('p_pengajuan.id', $ids);
		}

		if (request()->has('q_'.$status))
		{
			$cari	= request()->get('q_'.$status);
			$regexp = preg_replace("/-+/",'[^A-Za-z0-9_]+',$cari);
			$result	= $result->where(function($q)use($regexp)
			{				
				$q
				->whereRaw(DB::raw('nasabah REGEXP "'.$regexp.'"'));
			});
		}

		if (request()->has('jaminan_'.$status))
		{
			$cari	= request()->get('jaminan_'.$status);
			switch (strtolower($cari)) {
				case 'jaminan-bpkb':
					$result 	= $result->wherehas('jaminan_kendaraan', function($q){$q;});
					break;
				case 'jaminan-sertifikat':
					$result 	= $result->wherehas('jaminan_tanah_bangunan', function($q){$q;});
					break;
			}
		}

		if (request()->has('sort_'.$status)){
			$sort	= request()->get('sort_'.$status);
			switch (strtolower($sort)) {
				case 'nama-desc':
					$result 	= $result->orderby('p_pengajuan.nasabah->nama', 'desc');
					break;
				case 'tanggal-asc':
					$result 	= $result->selectraw('max(p_status.tanggal) as tanggal')->orderby('tanggal', 'asc');
					break;
				case 'tanggal-desc':
					$result 	= $result->selectraw('max(p_status.tanggal) as tanggal')->orderby('tanggal', 'desc');
					break;
				default :
					$result 	= $result->orderby('p_pengajuan.nasabah->nama', 'asc');
					break;
			}
		}else{
			$result 	= $result->orderby('p_pengajuan.nasabah->nama', 'asc');
		}

		$result		= $result->paginate(15, ['*'], $status);

		return $result;
	}
}