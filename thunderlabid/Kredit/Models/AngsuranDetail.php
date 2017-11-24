<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

use Validator;
use App\Service\Traits\IDRTrait;

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
	
	protected $table 	= 'k_angsuran_detail';
	protected $fillable = ['angsuran_id', 'ref_id', 'tag', 'amount', 'description'];
	protected $hidden 	= [];
	protected $appends	= [];
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

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setAmountAttribute($variable)
	{
		$this->attributes['amount']	= $this->formatMoneyFrom($variable);
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
		$rules['angsuran_id']	= ['required'];
		$rules['ref_id']		= ['nullable', 'string'];
		$rules['tag']			= ['nullable', 'string'];
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
}
