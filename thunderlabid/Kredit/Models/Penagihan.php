<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

use Validator;

use App\Service\Traits\WaktuTrait;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Kredit\Events\Penagihan\PenagihanCreated;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanCreating;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanUpdated;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanUpdating;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanDeleted;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanDeleting;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanRestored;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanRestoring;

class Penagihan extends Model
{
	use WaktuTrait;

	protected $table 	= 'k_penagihan';
	protected $fillable = ['nomor_kredit', 'nip_karyawan', 'tanggal'];
	protected $hidden 	= [];
	protected $appends	= [];
	protected $rules	= [];
	protected $errors;

	protected $events = [
        'created' 	=> PenagihanCreated::class,
        'creating' 	=> PenagihanCreating::class,
        'updated' 	=> PenagihanUpdated::class,
        'updating' 	=> PenagihanUpdating::class,
        'deleted' 	=> PenagihanDeleted::class,
        'deleting' 	=> PenagihanDeleting::class,
        'restoring' => PenagihanRestoring::class,
        'restored' 	=> PenagihanRestored::class,
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
	public function scopeHitungTunggakan($query){
		return $query->select('k_penagihan.*')
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td join k_angsuran where k_angsuran.id = td.angsuran_id and k_angsuran.nomor_kredit = k_penagihan.nomor_kredit and td.deleted_at is null and k_angsuran.deleted_at is null and k_angsuran.paid_at >= k_penagihan.tanggal and k_angsuran.issued_at <= k_penagihan.tanggal) as tunggakan"))
			->selectraw(\DB::raw("(select issued_at from k_angsuran where k_angsuran.nomor_kredit = k_penagihan.nomor_kredit and k_angsuran.deleted_at is null and k_angsuran.paid_at >= k_penagihan.tanggal and k_angsuran.issued_at <= k_penagihan.tanggal order by issued_at asc limit 1) as issued_at"));
	}

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setTanggalAttribute($variable)
	{
		$this->attributes['tanggal']	= $this->formatDateTimeFrom($variable);
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
		$rules['nip_karyawan'] 		= ['required', 'string'];
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

}
