<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

use Config;
use Validator;
use Carbon\Carbon;
use App\Service\Traits\WaktuTrait;
use App\Service\Traits\IDRTrait;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Kredit\Events\NotaBayar\NotaBayarCreated;
use Thunderlabid\Kredit\Events\NotaBayar\NotaBayarCreating;
use Thunderlabid\Kredit\Events\NotaBayar\NotaBayarUpdated;
use Thunderlabid\Kredit\Events\NotaBayar\NotaBayarUpdating;
use Thunderlabid\Kredit\Events\NotaBayar\NotaBayarDeleted;
use Thunderlabid\Kredit\Events\NotaBayar\NotaBayarDeleting;
use Thunderlabid\Kredit\Events\NotaBayar\NotaBayarRestored;
use Thunderlabid\Kredit\Events\NotaBayar\NotaBayarRestoring;

use Thunderlabid\Kredit\Models\Traits\FakturTrait;

class NotaBayar extends Model
{
	use WaktuTrait;
	use IDRTrait;
	use FakturTrait;
	
	protected $table 	= 'k_nota_bayar';
	protected $fillable = ['nomor_kredit', 'nomor_faktur', 'tanggal', 'nip_karyawan', 'jumlah', 'nip_karyawan', 'penagihan_id'];
	protected $hidden 	= [];
	protected $appends	= ['jatuh_tempo'];

	protected $rules	= [];
	protected $errors;

	protected $events = [
		'created' 	=> NotaBayarCreated::class,
		'creating' 	=> NotaBayarCreating::class,
		'updated' 	=> NotaBayarUpdated::class,
		'updating' 	=> NotaBayarUpdating::class,
		'deleted' 	=> NotaBayarDeleted::class,
		'deleting' 	=> NotaBayarDeleting::class,
		'restoring' => NotaBayarRestoring::class,
		'restored' 	=> NotaBayarRestored::class,
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

	public function details(){
		return $this->hasMany(AngsuranDetail::class, 'nota_bayar_id');
	}
	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------
	public function scopeDisplaying($query){
		return $query->select('k_nota_bayar.*')
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.tag = 'pokok' and td.deleted_at is null) as pokok"))
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.tag = 'bunga' and td.deleted_at is null) as bunga"))
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.tag = 'denda' and td.deleted_at is null) as denda"))
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.tag = 'collector' and td.deleted_at is null) as collector"))
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.tag = 'potongan' and td.deleted_at is null) as potongan"))
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.deleted_at is null) as subtotal"));
	}

	public function scopeCountAmount($query){
		return $query->select('k_nota_bayar.*')
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.deleted_at is null) as amount"));
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
		$rules['nomor_faktur'] 		= ['required', 'string'];
		$rules['nomor_kredit'] 		= ['required', 'string'];
		$rules['tanggal'] 			= ['required', 'date_format:"Y-m-d H:i:s"'];

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

	public function getTanggalAttribute($variable)
	{
		return $this->formatDateTimeTo($this->attributes['tanggal']);
	}

	public function getJumlahAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['jumlah']);
	}

	public function getJatuhTempoAttribute($variable){
		return Carbon::parse($this->attributes['tanggal'])->addDays(Config::get('kredit.batas_pembayaran_angsuran_hari'))->format('d/m/Y H:i');
	}

}
