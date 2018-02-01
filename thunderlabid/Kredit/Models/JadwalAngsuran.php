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
use Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranCreated;
use Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranCreating;
use Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranUpdated;
use Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranUpdating;
use Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranDeleted;
use Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranDeleting;
use Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranRestored;
use Thunderlabid\Kredit\Events\JadwalAngsuran\JadwalAngsuranRestoring;

class JadwalAngsuran extends Model
{
	use IDRTrait;
	use WaktuTrait;
	
	protected $table 	= 'k_jadwal_angsuran';
	protected $fillable = ['nomor_kredit', 'nomor_faktur', 'tanggal', 'nth', 'jumlah', 'deskripsi'];
	protected $hidden 	= [];
	protected $appends	= ['is_tunggakan'];
	protected $rules	= [];
	protected $errors;

	protected $events = [
        'created' 	=> JadwalAngsuranCreated::class,
        'creating' 	=> JadwalAngsuranCreating::class,
        'updated' 	=> JadwalAngsuranUpdated::class,
        'updating' 	=> JadwalAngsuranUpdating::class,
        'deleted' 	=> JadwalAngsuranDeleted::class,
        'deleting' 	=> JadwalAngsuranDeleting::class,
        'restoring' => JadwalAngsuranRestoring::class,
        'restored' 	=> JadwalAngsuranRestored::class,
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

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------
	public function scopeDisplaying($query){
		return $query
			->selectraw(\DB::raw('SUM(IF(tag="potongan",amount,0)) as potongan'))
			->selectraw(\DB::raw('SUM(IF(tag="pokok",amount,0)) as pokok'))
			->selectraw(\DB::raw('SUM(IF(tag="bunga",amount,0)) as bunga'))
			->selectraw('SUM(IF(tag="bunga",amount,IF(tag="pokok",amount,IF(tag="potongan",amount,0)))) as subtotal')
			->selectraw('min(tanggal) as tanggal_bayar')
			->selectraw('min(nota_bayar_id) as nota_bayar_id')
			->selectraw('nth')->selectraw('max(nomor_kredit) as nomor_kredit')
			->whereIn('tag', ['potongan', 'pokok', 'bunga'])
			->groupby('nth');
	}

	public function scopeDisplayingDenda($query){
		return $query->selectraw(\DB::raw('SUM(IF(tag="restitusi_denda",amount,0)) as restitusi_denda'))->selectraw(\DB::raw('SUM(IF(tag="denda",amount,0)) as denda'))->selectraw('SUM(IF(tag="restitusi_denda",amount,IF(tag="denda",amount,0))) as subtotal')->selectraw('min(tanggal) as tanggal_bayar')->selectraw('min(nota_bayar_id) as nota_bayar_id')->whereIn('tag', ['denda', 'restitusi_denda'])->selectraw('nth')->where('nth', '>', 0)->groupby('nth');
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
			->tunggakanBeberapaWaktuLalu($value)
			->selectraw("(select sum(kd2.amount) from k_angsuran_detail as kd2 where kd2.nomor_kredit = k_angsuran_detail.nomor_kredit and kd2.nota_bayar_id is null and kd2.deleted_at is null) as sisa_hutang")
			->wherein('tag', ['pokok', 'bunga'])
			->groupby('nomor_kredit');
	}

	public function scopeTunggakanBeberapaWaktuLalu($query, Carbon $value){
		return $query->where(function($q)use($value){
				$q
				->wherenull('nota_bayar_id')
				->orwhereraw(\DB::raw('(select nb.tanggal from k_nota_bayar as nb where nb.id = k_angsuran_detail.nota_bayar_id and nb.tanggal >= "'.$value->format('Y-m-d H:i:s').'" limit 1) >= k_angsuran_detail.tanggal'))
				;
			})
			->where('tanggal', '<=', $value->format('Y-m-d H:i:s'))
		;
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

	public function setJumlahAttribute($variable)
	{
		$this->attributes['jumlah']		= $this->formatMoneyFrom($variable);
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
		$rules['jumlah'] 		= ['required', 'numeric'];

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

	public function getJumlahAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['jumlah']);
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
		if($total >= 6){
			$data['cetak']		= ['surat_peringatan_1', 'surat_peringatan_2', 'surat_peringatan_3', 'surat_somasi_1', 'surat_somasi_2', 'surat_somasi_3'];
			$data['keluarkan'] 	= null;
		}elseif($total < 6 && $total > 4){
			$data['cetak']		= ['surat_peringatan_1', 'surat_peringatan_2', 'surat_peringatan_3', 'surat_somasi_1'];
			$data['keluarkan'] 	= 'surat_somasi_3';
		}elseif($total < 5 && $total > 3){
			$data['cetak']		= ['surat_peringatan_1', 'surat_peringatan_2', 'surat_peringatan_3', 'surat_somasi_1'];
			$data['keluarkan'] 	= 'surat_somasi_2';
		}elseif($total < 4 && $total > 2){
			$data['cetak']		= ['surat_peringatan_1', 'surat_peringatan_2', 'surat_peringatan_3'];
			$data['keluarkan'] 	= 'surat_somasi_1';
		}elseif($total < 3 && $total > 1){
			$data['cetak']		= ['surat_peringatan_1', 'surat_peringatan_2'];
			$data['keluarkan'] 	= 'surat_peringatan_3';
		}elseif($total < 2 && $total > 0){
			$data['cetak']		= ['surat_peringatan_1'];
			$data['keluarkan'] 	= 'surat_peringatan_2';
		}elseif($total==0){
			$data['cetak']		= [];
			$data['keluarkan'] 	= 'surat_peringatan_1';
		}

		return $data;
	}
}
