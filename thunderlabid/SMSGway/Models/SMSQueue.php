<?php

namespace Thunderlabid\SMSGway\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueCreated;
use Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueCreating;
use Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueUpdated;
use Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueUpdating;
use Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueDeleted;
use Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueDeleting;
use Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueRestored;
use Thunderlabid\SMSGway\Events\SMSQueue\SMSQueueRestoring;

use Validator;
class SMSQueue extends Model
{
	protected $table 	= 'sms_queues';
	protected $fillable = ['morph_reference_id', 'morph_reference_tag', 'penerima', 'isi', 'status', 'respon'];
	protected $hidden 	= [];
	protected $appends	= [];

	protected $rules	= [];
	protected $errors;

	protected $events = [
		'created' 	=> SMSQueueCreated::class,
		'creating' 	=> SMSQueueCreating::class,
		'updated' 	=> SMSQueueUpdated::class,
		'updating' 	=> SMSQueueUpdating::class,
		'deleted' 	=> SMSQueueDeleted::class,
		'deleting' 	=> SMSQueueDeleting::class,
		'restoring' => SMSQueueRestoring::class,
		'restored' 	=> SMSQueueRestored::class,
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
	public function setResponAttribute(array $variable)
	{
		$this->attributes['respon']		= json_encode($variable);
	}

	public function setPenerimaAttribute(array $variable)
	{
		$this->attributes['penerima']	= json_encode($variable);
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
		$rules['morph_reference_id']	= ['required', 'string'];
		$rules['morph_reference_tag']	= ['required', 'string'];
		$rules['penerima.telepon'] 		= ['required', 'string'];
		$rules['isi'] 					= ['required', 'string'];
		$rules['status'] 				= ['required', 'string'];

		//////////////
		// Validate //
		//////////////
		$attr 				= $this->attributes;
		$attr['penerima']	= json_decode($this->attributes['penerima'], true);

		$validator = Validator::make($attr, $rules);
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

	public function getResponAttribute($variable)
	{
		return json_decode($this->attributes['respon'], true);
	}

	public function getPenerimaAttribute($variable)
	{
		return json_decode($this->attributes['penerima'], true);
	}
}
