<?php

namespace App\Http\Controllers\V2\Traits;

use App\Http\Service\Policy\BayarDenda;
use App\Http\Service\Policy\BayarAngsuran;
use App\Http\Service\Policy\FeedBackPenagihan;

use Auth;
/**
 * Trait tanggal
 *
 * Digunakan untuk reformat tanggal sesuai kontrak
 *
 * @package    Thunderlabid
 * @subpackage Credit
 * @author     C Mooy <chelsy@thunderlab.od>
 */
trait KreditTrait {
	
 	public function store_denda($aktif){
		$denda 		= new BayarDenda($aktif, Auth::user()['nip'], request()->get('potongan'), request()->get('tanggal'), request()->get('nomor_perkiraan'));
		$denda->bayar();
 	}

 	public function store_tagihan($aktif){
 		$feedback 	= new FeedBackPenagihan($aktif, ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']], request()->get('tanggal'), request()->get('penerima'), request()->get('nominal'), request()->get('nomor_perkiraan'), request()->get('sp_id'));
		$feedback->bayar();
 	}

 	public function store_angsuran($aktif){
 		$bayar 		= new BayarAngsuran($aktif, Auth::user()['nip'], request()->get('nth'), request()->get('tanggal'), request()->get('nomor_perkiraan'));
		$bayar->bayar();
 	}
}