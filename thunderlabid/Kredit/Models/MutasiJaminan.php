<?php

namespace Thunderlabid\Member\Models;

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
use Thunderlabid\Member\Events\MutasiJaminan\MutasiJaminanCreated;
use Thunderlabid\Member\Events\MutasiJaminan\MutasiJaminanCreating;
use Thunderlabid\Member\Events\MutasiJaminan\MutasiJaminanUpdated;
use Thunderlabid\Member\Events\MutasiJaminan\MutasiJaminanUpdating;
use Thunderlabid\Member\Events\MutasiJaminan\MutasiJaminanDeleted;
use Thunderlabid\Member\Events\MutasiJaminan\MutasiJaminanDeleting;
use Thunderlabid\Member\Events\MutasiJaminan\MutasiJaminanRestored;
use Thunderlabid\Member\Events\MutasiJaminan\MutasiJaminanRestoring;

class MutasiJaminan extends Model
{
	use WaktuTrait;

	protected $table 	= 'k_mutasi_jaminan';
	protected $fillable = ['nomor_kredit', 'stored_at', 'taken_at', 'documents'];
	protected $hidden 	= [];
	protected $appends	= [];
	protected $rules	= [];
	protected $errors;
	protected $dispatchesEvents = [
        'created' 	=> MutasiJaminanCreated::class,
        'creating' 	=> MutasiJaminanCreating::class,
        'updated' 	=> MutasiJaminanUpdated::class,
        'updating' 	=> MutasiJaminanUpdating::class,
        'deleted' 	=> MutasiJaminanDeleted::class,
        'deleting' 	=> MutasiJaminanDeleting::class,
        'restoring' => MutasiJaminanRestoring::class,
        'restored' 	=> MutasiJaminanRestored::class,
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

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setStoredAtAttribute($variable)
	{
		$this->attributes['stored_at']	= $this->formatDateTimeFrom($variable);
	}

	public function setTakenAtAttribute($variable)
	{
		$this->attributes['taken_at']	= $this->formatDateTimeFrom($variable);
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
		$rules['stored_at'] 		= ['required', 'date_format:"Y-m-d H:i:s"'];
		$rules['taken_at'] 			= ['required', 'date_format:"Y-m-d H:i:s"'];

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

	public function getStoredAtAttribute($variable)
	{
		return $this->formatDateTimeTo($this->attributes['stored_at']);
	}

	public function getTakenAtAttribute($variable)
	{
		return $this->formatDateTimeTo($this->attributes['taken_at']);
	}
}
