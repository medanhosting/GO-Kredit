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
use Thunderlabid\Kredit\Events\Angsuran\AngsuranCreated;
use Thunderlabid\Kredit\Events\Angsuran\AngsuranCreating;
use Thunderlabid\Kredit\Events\Angsuran\AngsuranUpdated;
use Thunderlabid\Kredit\Events\Angsuran\AngsuranUpdating;
use Thunderlabid\Kredit\Events\Angsuran\AngsuranDeleted;
use Thunderlabid\Kredit\Events\Angsuran\AngsuranDeleting;
use Thunderlabid\Kredit\Events\Angsuran\AngsuranRestored;
use Thunderlabid\Kredit\Events\Angsuran\AngsuranRestoring;

class Angsuran extends Model
{
	use WaktuTrait;
	
	protected $table 	= 'k_angsuran';
	protected $fillable = ['nomor_kredit', 'issued_at', 'paid_at'];
	protected $hidden 	= [];
	protected $appends	= [];

	protected $rules	= [];
	protected $errors;

	protected $events = [
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
    public function details(){
    	return $this->hasMany(AngsuranDetail::class, 'angsuran_id');
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
		return $this->formatDateTimeTo($this->attributes['issued_at']);
	}

	public function getPaidAtAttribute($variable)
	{
		return $this->formatDateTimeTo($this->attributes['paid_at']);
	}

}
