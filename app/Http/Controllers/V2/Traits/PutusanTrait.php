<?php

namespace App\Http\Controllers\V2\Traits;

use Thunderlabid\Pengajuan\Models\Putusan;
use Thunderlabid\Pengajuan\Models\Status;

use Thunderlabid\Finance\Models\NotaBayar;
use Thunderlabid\Finance\Models\DetailTransaksi;

use Auth;
use Validator;
use Carbon\Carbon;

/**
 * Trait tanggal
 *
 * Digunakan untuk reformat tanggal sesuai kontrak
 *
 * @package    Thunderlabid
 * @subpackage Credit
 * @author     C Mooy <chelsy@thunderlab.od>
 */
trait PutusanTrait {
	private function checker_realisasi($checklists) 
	{
		//CHECKER Checklists
		$rule_n 	= Putusan::rule_of_checklist();
		$total 		= count($rule_n);

		if (count($checklists)) {
			$validator 	= Validator::make($checklists, $rule_n);

			if ($validator->fails()) {
				$complete	= (count($rule_n) - count($validator->messages()));
				$checker 	= false;
			} else {
				$complete	= count($rule_n);
				$checker 	= true;
			}
		} else {
			$checker 		= false;
		}

		$percentage 	= floor(($complete / max($total, 1)) * 100);

		view()->share('checker', $checker);
		view()->share('percentage', $percentage);
	}

	private function store_checklists($putusan){
		$objek 		= request()->get('checklists')['objek'];
		$pengikat 	= request()->get('checklists')['pengikat'];

		foreach ($putusan['checklists']['objek'] as $k => $v) {
			if($objek[$k]){
				$data_input['checklists']['objek'][$k]	= 'ada';
			}elseif(!str_is($v, 'cadangkan')){
				$data_input['checklists']['objek'][$k]	= 'tidak_ada';
			}else{
				$data_input['checklists']['objek'][$k]	= 'cadangkan';
			}
		}

		foreach ($putusan['checklists']['pengikat'] as $k => $v) {
			if($pengikat[$k]){
				$data_input['checklists']['pengikat'][$k]	= 'ada';
			}elseif(!str_is($v, 'cadangkan')){
				$data_input['checklists']['pengikat'][$k]	= 'tidak_ada';
			}else{
				$data_input['checklists']['pengikat'][$k]	= 'cadangkan';
			}
		}
		$putusan->fill($data_input);
		$putusan->save();
	}

	private function validasi_checklists($id){
		$status 				= new Status;
		$status->tanggal 		= request()->get('tanggal');
		$status->progress 		= 'sudah';
		$status->status 		= 'realisasi';
		$status->karyawan 		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
		$status->pengajuan_id 	= $id;
		$status->save();
	}

	private function store_setoran_realisasi($putusan){
		//simpan nota bayar
		$total 		= $this->formatMoneyFrom($putusan->provisi) + $this->formatMoneyFrom($putusan->administrasi) + $this->formatMoneyFrom($putusan->legal) + $this->formatMoneyFrom($putusan->biaya_notaris);

		$nb 		= NotaBayar::where('morph_reference_id', $putusan['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('jenis', 'setoran_pencairan')->first();
		if(!$nb){
			$nb 				= new NotaBayar;
			$nb->nomor_faktur  	= NotaBayar::generatenomorfaktur($putusan['nomor_kredit']);
			$nb->tanggal 		= request()->get('tanggal');
			$nb->karyawan 		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
		}

		$nb->morph_reference_id		= $putusan->nomor_kredit;
		$nb->morph_reference_tag	= 'kredit';
		$nb->jenis 					= 'setoran_pencairan';
		$nb->jumlah 				= $this->formatMoneyTo($total);
		$nb->nomor_rekening			= request()->get('nomor_perkiraan');
		$nb->save();

		$idx 				= ['provisi', 'administrasi', 'legal', 'biaya_notaris'];

		foreach ($idx as $k => $v) {
			$ad 			= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('tag', $v)->first();
			if(!$ad){
				$ad 		= new DetailTransaksi;
			}
			$ad->nomor_faktur 	= $nb->nomor_faktur;
			$ad->tag 			= $v;
			$ad->jumlah 		= $putusan[$v];
			$ad->morph_reference_id 	= $nb->morph_reference_id;
			$ad->morph_reference_tag 	= $nb->morph_reference_tag;
			$ad->deskripsi 		= ucwords(str_replace('_', ' ', $v)).' Kredit';
			$ad->save();
		}
	}

	private function store_realisasi($putusan){
		//simpan nota bayar
		$total 		= $this->formatMoneyFrom($putusan->plafon_pinjaman);

		$nb 		= NotaBayar::where('morph_reference_id', $putusan['nomor_kredit'])->where('morph_reference_tag', 'kredit')->where('jenis', 'pencairan')->first();
		if(!$nb){
			$nb 				= new NotaBayar;
			$nb->nomor_faktur  	= NotaBayar::generatenomorfaktur($putusan['nomor_kredit']);
			$nb->tanggal 		= request()->get('tanggal');
			$nb->karyawan 		= ['nip' => Auth::user()['nip'], 'nama' => Auth::user()['nama']];
		}

		$nb->morph_reference_id		= $putusan->nomor_kredit;
		$nb->morph_reference_tag	= 'kredit';
		$nb->jenis 					= 'pencairan';
		$nb->jumlah 				= $this->formatMoneyTo(0 - $total);
		$nb->nomor_rekening			= request()->get('nomor_perkiraan');
		$nb->save();

		//angsuran detail
		$ad			= DetailTransaksi::where('nomor_faktur', $nb->nomor_faktur)->where('tag', 'pencairan')->first();
		if(!$ad){
			$ad		= new DetailTransaksi;
		}
		$ad->nomor_faktur 	= $nb->nomor_faktur;
		$ad->tag 			= 'pencairan';
		$ad->jumlah 		= $this->formatMoneyTo(0 - $total);
		$ad->morph_reference_id 	= $nb->morph_reference_id;
		$ad->morph_reference_tag 	= $nb->morph_reference_tag;
		$ad->deskripsi 		= 'Pencairan Kredit';
		$ad->save();
	}
}