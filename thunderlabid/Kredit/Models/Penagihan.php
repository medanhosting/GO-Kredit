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
	protected $fillable = ['nomor_kredit', 'nip_karyawan', 'collected_at'];
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

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setCollectedAtAttribute($variable)
	{
		$this->attributes['collected_at']	= $this->formatDateTimeFrom($variable);
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
		$rules['collected_at'] 		= ['required', 'date_format:"Y-m-d H:i:s"'];

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

	public function getCollectedAtAttribute($variable)
	{
		return $this->formatDateTimeTo($this->attributes['collected_at']);
	}

}
