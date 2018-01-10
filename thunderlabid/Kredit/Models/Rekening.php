<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

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
use Thunderlabid\Kredit\Events\Rekening\RekeningCreated;
use Thunderlabid\Kredit\Events\Rekening\RekeningCreating;
use Thunderlabid\Kredit\Events\Rekening\RekeningUpdated;
use Thunderlabid\Kredit\Events\Rekening\RekeningUpdating;
use Thunderlabid\Kredit\Events\Rekening\RekeningDeleted;
use Thunderlabid\Kredit\Events\Rekening\RekeningDeleting;
use Thunderlabid\Kredit\Events\Rekening\RekeningRestored;
use Thunderlabid\Kredit\Events\Rekening\RekeningRestoring;

class Rekening extends Model
{
	use WaktuTrait;
	use IDRTrait;
	
	protected $table 	= 'k_rekening';
	protected $fillable = ['nama'];
	protected $hidden 	= [];
	protected $appends	= [];

	protected $rules	= [];
	protected $errors;

	protected $events = [
		'created' 	=> RekeningCreated::class,
		'creating' 	=> RekeningCreating::class,
		'updated' 	=> RekeningUpdated::class,
		'updating' 	=> RekeningUpdating::class,
		'deleted' 	=> RekeningDeleted::class,
		'deleting' 	=> RekeningDeleting::class,
		'restoring' => RekeningRestoring::class,
		'restored' 	=> RekeningRestored::class,
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
		$rules['nama'] 				= ['required', 'string'];

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
}
