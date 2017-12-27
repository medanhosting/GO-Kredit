<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

use Validator, Config;
use Carbon\Carbon;
use App\Service\Traits\IDRTrait;
use App\Service\Traits\WaktuTrait;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailCreated;
use Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailCreating;
use Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailUpdated;
use Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailUpdating;
use Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailDeleted;
use Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailDeleting;
use Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailRestored;
use Thunderlabid\Kredit\Events\AngsuranDetail\AngsuranDetailRestoring;

class AngsuranDetail extends Model
{
	use IDRTrait;
	use WaktuTrait;
	
	protected $table 	= 'k_angsuran_detail';
	protected $fillable = ['nota_bayar_id', 'nomor_kredit', 'tanggal', 'nth', 'tag', 'amount', 'description'];
	protected $hidden 	= [];
	protected $appends	= ['is_tunggakan'];
	protected $rules	= [];
	protected $errors;

	protected $events = [
        'created' 	=> AngsuranDetailCreated::class,
        'creating' 	=> AngsuranDetailCreating::class,
        'updated' 	=> AngsuranDetailUpdated::class,
        'updating' 	=> AngsuranDetailUpdating::class,
        'deleted' 	=> AngsuranDetailDeleted::class,
        'deleting' 	=> AngsuranDetailDeleting::class,
        'restoring' => AngsuranDetailRestoring::class,
        'restored' 	=> AngsuranDetailRestored::class,
    ];

	// ------------------------------------------------------------------------------------------------------------
	// CONSTRUCT
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// BOOT
	// ------------------------------------------------------------------------------------------------------------
	
	// ------------------------------------------------------------------------------------------------------------
	// RELATION
	// ------------------------------------------------------------------------------------------------------------
	public function kredit(){
		return $this->belongsto(Aktif::class, 'nomor_kredit', 'nomor_kredit');
	}

	public function notabayar(){
		return $this->belongsto(NotaBayar::class, 'nota_bayar_id');
	}

	public function suratperingatan(){
		return $this->hasMany(SuratPeringatan::class, 'nomor_kredit', 'nomor_kredit')->where('nth', $this->nth)->orderby('tanggal', 'asc');
	}

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------
	public function scopeDisplaying($query){
		return $query->selectraw(\DB::raw('SUM(IF(tag="denda",amount,0)) as denda'))->selectraw(\DB::raw('SUM(IF(tag="pokok",amount,0)) as pokok'))->selectraw(\DB::raw('SUM(IF(tag="bunga",amount,0)) as bunga'))->selectraw('sum(amount) as subtotal')->selectraw('min(tanggal) as tanggal_bayar')->selectraw('min(nota_bayar_id) as nota_bayar_id')->selectraw('nth')->groupby('nth');
	}

	public function scopeLihatJatuhTempo($query, Carbon $value){
		return $query->where(function($q)use($value){
			$q->where('tanggal', '<=', $value->format('Y-m-d H:i:s'))->orwherein('tag', ['denda', 'collector']);	
		});
	}

	public function scopeCountAmount($query){
		return $query
			->selectraw('nomor_kredit')
			->selectraw('min(tanggal) as tanggal')
			->selectraw("sum(amount) as amount")
			->selectraw("max(nota_bayar_id) as nota_bayar_id")
			->groupby('nomor_kredit');
	}

	public function scopeHitungTunggakan($query){
		return $query
			->selectraw('nomor_kredit')
			->selectraw('min(tanggal) as tanggal')
			->selectraw("sum(amount) as tunggakan")
			->selectraw("max(nota_bayar_id) as nota_bayar_id")
			->wherenull('nota_bayar_id')
			->groupby('nomor_kredit');
	}

	public function scopeHitungTunggakanBeberapaWaktuLalu($query, Carbon $value){
		return $query
			->selectraw('nomor_kredit')
			->selectraw('min(nth) as nth')
			->selectraw('min(tanggal) as tanggal')
			->selectraw("sum(amount) as tunggakan")
			->selectraw("max(nota_bayar_id) as nota_bayar_id")
			->where(function($q)use($value){
				$q->wherenull('nota_bayar_id')
				->orwhereraw(\DB::raw('(select nb.tanggal from k_nota_bayar as nb where nb.id = k_angsuran_detail.nota_bayar_id and nb.tanggal >= "'.$value->format('Y-m-d H:i:s').'" limit 1) >= k_angsuran_detail.tanggal'))
				;
			})
			->selectraw("(select sum(kd2.amount) from k_angsuran_detail as kd2 where kd2.nomor_kredit = k_angsuran_detail.nomor_kredit and kd2.nota_bayar_id is null and kd2.deleted_at is null) as sisa_hutang")
			->where('tanggal', '<=', $value->format('Y-m-d H:i:s'))
			->groupby('nomor_kredit');
	}
	
	public function scopeHitungTotalHutang($query, $nomor_kredit){
		return $query->where('nomor_kredit', $nomor_kredit)->sum('amount');
	}

	public function scopeHitungHutangDibayar($query, $nomor_kredit, $nota_bayar_id = null){
		if(!is_null($nota_bayar_id)){
			return $query->where('nomor_kredit', $nomor_kredit)->where('nota_bayar_id', '<', $nota_bayar_id)->sum('amount');
		}
		return $query->where('nomor_kredit', $nomor_kredit)->wherenotnull('nota_bayar_id')->sum('amount');
	}


	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setTanggalAttribute($variable)
	{
		$this->attributes['tanggal']	= $this->formatDateTimeFrom($variable);
	}

	public function setAmountAttribute($variable)
	{
		$this->attributes['amount']		= $this->formatMoneyFrom($variable);
	}

	// ------------------------------------------------------------------------------------------------------------
	// ACCESSOR
	// ------------------------------------------------------------------------------------------------------------
	public function getIsDeletableAttribute()
	{
		return true;
	}

	public function getIsSavableAttribute()
	{
		//////////////////
		// Create Rules //
		//////////////////
		$rules['nota_bayar_id']	= ['nullable', 'exists:k_nota_bayar,id'];
		$rules['nomor_kredit']	= ['required', 'exists:k_aktif,nomor_kredit'];
		$rules['tanggal'] 		= ['required', 'date_format:"Y-m-d H:i:s"'];
		$rules['nth']			= ['nullable', 'numeric'];
		$rules['tag']			= ['required', 'string'];
		$rules['amount'] 		= ['required', 'numeric'];

		//////////////
		// Validate //
		//////////////
		$validator = Validator::make($this->attributes, $rules);
		if ($validator->fails())
		{
			$this->errors = $validator->messages();
			return false;
		}

		return true;
	}

	public function getErrorsAttribute()
	{
		return $this->errors;
	}

	public function getAmountAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['amount']);
	}

	public function getTanggalAttribute($variable)
	{
		return $this->formatDateTimeTo($this->attributes['tanggal']);
	}

	public function getIsTunggakanAttribute($variable)
	{
		$tanggal 	= Carbon::now();
		$jt 		= Carbon::parse($this->tanggal_bayar)->addDays(Config::get('kredit.batas_pembayaran_angsuran_hari'));

		if($jt < $tanggal && is_null($this->nota_bayar_id)){
			return true;
		}
		return false;
	}

	public function getShouldIssueSuratPeringatanAttribute($variable)
	{
		$data 	= null;
		$total 	= $this->suratperingatan()->count();
		if($total >= 7){
			$data['cetak']		= ['surat_pemberitahuan', 'surat_peringatan_1', 'surat_peringatan_2', 'surat_peringatan_3', 'surat_somasi_1', 'surat_somasi_2', 'surat_somasi_3'];
			$data['keluarkan'] 	= null;
		}
		elseif($total < 7 && $total > 5){
			$data['cetak']		= ['surat_pemberitahuan', 'surat_peringatan_1', 'surat_peringatan_2', 'surat_peringatan_3', 'surat_somasi_1', 'surat_somasi_2'];
			$data['keluarkan'] 	= 'surat_somasi_3';
		}elseif($total < 6 && $total > 4){
			$data['cetak']		= ['surat_pemberitahuan', 'surat_peringatan_1', 'surat_peringatan_2', 'surat_peringatan_3', 'surat_somasi_1'];
			$data['keluarkan'] 	= 'surat_somasi_2';
		}elseif($total < 5 && $total > 3){
			$data['cetak']		= ['surat_pemberitahuan', 'surat_peringatan_1', 'surat_peringatan_2', 'surat_peringatan_3'];
			$data['keluarkan'] 	= 'surat_somasi_1';
		}elseif($total < 4 && $total > 2){
			$data['cetak']		= ['surat_pemberitahuan', 'surat_peringatan_1', 'surat_peringatan_2'];
			$data['keluarkan'] 	= 'surat_peringatan_3';
		}elseif($total < 3 && $total > 1){
			$data['cetak']		= ['surat_pemberitahuan', 'surat_peringatan_1'];
			$data['keluarkan'] 	= 'surat_peringatan_2';
		}elseif($total < 2 && $total > 0){
			$data['cetak']		= ['surat_pemberitahuan'];
			$data['keluarkan'] 	= 'surat_peringatan_1';
		}elseif($total==0){
			$data['cetak']		= [];
			$data['keluarkan'] 	= 'surat_pemberitahuan';
		}

		return $data;
	}
}
