<?php

namespace App\Http\Controllers\V2\Traits;

use App\Http\Service\Policy\BayarDenda;
use App\Http\Service\Policy\BayarAngsuran;
use App\Http\Service\Policy\FeedBackPenagihan;

use Auth, Config;
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
		$denda 		= new BayarDenda($aktif, ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']], request()->get('tanggal'), request()->get('nomor_perkiraan'));
		$denda->bayar(request()->get('nominal'));
 	}
	
 	public function store_permintaan_restitusi($aktif){
		$denda 		= new BayarDenda($aktif, ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']], request()->get('tanggal'));
		$denda->permintaan_restitusi(request()->get('jenis'), request()->get('nominal'), request()->get('alasan'));
 	}

 	public function store_validasi_restitusi($aktif){
		$denda 		= new BayarDenda($aktif, ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']], request()->get('tanggal'));
		$denda->validasi_restitusi(request()->get('is_approved'));
 	}

 	public function store_tagihan($aktif){
 		$feedback 	= new FeedBackPenagihan($aktif, ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']], request()->get('tanggal'), request()->get('penerima'), request()->get('nominal'), Config::get('finance.nomor_perkiraan_titipan_kolektor'), request()->get('sp_id'));
		$feedback->bayar();
 	}

 	public function penerimaan_titipan_tagihan($aktif){
 		$feedback 	= new FeedBackPenagihan($aktif, ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']], null, null, null, Config::get('finance.nomor_perkiraan_titipan'), null);
		$feedback->penerimaan_titipan_tagihan(request()->get('penagihan_id'));
 	}

 	public function store_angsuran($aktif){
 		$bayar 		= new BayarAngsuran($aktif, ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']], request()->get('nth'), request()->get('tanggal'));
		$bayar->bayar();
 	}

 	public function store_bayar_sebagian($aktif){
 		$bayar 		= new BayarAngsuran($aktif, ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']], null, request()->get('tanggal'), Config::get('finance.nomor_perkiraan_titipan'));
		$bayar->bayar_sebagian(request()->get('nominal'));
 	}
}