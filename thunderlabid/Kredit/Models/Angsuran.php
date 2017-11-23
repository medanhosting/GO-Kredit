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
use Thunderlabid\Member\Events\Angsuran\AngsuranCreated;
use Thunderlabid\Member\Events\Angsuran\AngsuranCreating;
use Thunderlabid\Member\Events\Angsuran\AngsuranUpdated;
use Thunderlabid\Member\Events\Angsuran\AngsuranUpdating;
use Thunderlabid\Member\Events\Angsuran\AngsuranDeleted;
use Thunderlabid\Member\Events\Angsuran\AngsuranDeleting;
use Thunderlabid\Member\Events\Angsuran\AngsuranRestored;
use Thunderlabid\Member\Events\Angsuran\AngsuranRestoring;

class Angsuran extends Model
{
	use WaktuTrait;
	
	protected $table 	= 'k_angsuran';
	protected $fillable = ['nomor_kredit', 'issued_at', 'paid_at'];
	protected $hidden 	= [];
	protected $appends	= [];

	protected $rules	= [];
	protected $errors;

	protected $dispatchesEvents = [
        'created' 	=> AngsuranCreated::class,
        'creating' 	=> AngsuranCreating::class,
        'updated' 	=> AngsuranUpdated::class,
        'updating' 	=> AngsuranUpdating::class,
        'deleted' 	=> AngsuranDeleted::class,
        'deleting' 	=> AngsuranDeleting::class,
        'restoring' => AngsuranRestoring::class,
        'restored' 	=> AngsuranRestored::class,
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
	public function setIssuedAtAttribute($variable)
	{
		$this->attributes['issued_at']	= $this->formatDateTimeFrom($variable);
	}

	public function setPaidAtAttribute($variable)
	{
		$this->attributes['paid_at']	= $this->formatDateTimeFrom($variable);
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
		$rules['issued_at'] 		= ['required', 'date_format:"Y-m-d H:i:s"'];
		$rules['paid_at'] 			= ['nullable', 'date_format:"Y-m-d H:i:s"'];

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

	public function getIssuedAtAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['issued_at']);
	}

	public function getPaidAtAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['paid_at']);
	}

}
