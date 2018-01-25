<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

use Validator, Carbon\Carbon;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\WaktuTrait;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Kredit\Events\PermintaanRestitusi\PermintaanRestitusiCreated;
use Thunderlabid\Kredit\Events\PermintaanRestitusi\PermintaanRestitusiCreating;
use Thunderlabid\Kredit\Events\PermintaanRestitusi\PermintaanRestitusiUpdated;
use Thunderlabid\Kredit\Events\PermintaanRestitusi\PermintaanRestitusiUpdating;
use Thunderlabid\Kredit\Events\PermintaanRestitusi\PermintaanRestitusiDeleted;
use Thunderlabid\Kredit\Events\PermintaanRestitusi\PermintaanRestitusiDeleting;
use Thunderlabid\Kredit\Events\PermintaanRestitusi\PermintaanRestitusiRestored;
use Thunderlabid\Kredit\Events\PermintaanRestitusi\PermintaanRestitusiRestoring;

class PermintaanRestitusi extends Model
{
	use IDRTrait;
	use WaktuTrait;

	protected $table 	= 'k_permintaan_restitusi';
	protected $fillable = ['nomor_kredit', 'tanggal', 'is_approved', 'nota_bayar_id', 'jenis', 'amount', 'alasan', 'karyawan'];
	protected $hidden 	= [];
	protected $appends	= [];
	protected $rules	= [];
	protected $errors;

	protected $events = [
        'created' 	=> PermintaanRestitusiCreated::class,
        'creating' 	=> PermintaanRestitusiCreating::class,
        'updated' 	=> PermintaanRestitusiUpdated::class,
        'updating' 	=> PermintaanRestitusiUpdating::class,
        'deleted' 	=> PermintaanRestitusiDeleted::class,
        'deleting' 	=> PermintaanRestitusiDeleting::class,
        'restoring' => PermintaanRestitusiRestoring::class,
        'restored' 	=> PermintaanRestitusiRestored::class,
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

	public function angsurandetail(){
		return $this->belongsto(AngsuranDetail::class, 'angsuran_detail_id');
	}

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------

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

	public function setKaryawanAttribute($variable)
	{
		$this->attributes['karyawan']	= json_encode($variable);
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

	public function getAmountAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['amount']);
	}
	
	public function getKaryawanAttribute($variable)
	{
		return json_decode($this->attributes['karyawan'], true);
	}
}
