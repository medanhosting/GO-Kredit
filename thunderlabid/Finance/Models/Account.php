<?php

namespace Thunderlabid\Finance\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

use Validator;

////////////
// EVENTS //
////////////
use Thunderlabid\Finance\Events\Account\AccountCreated;
use Thunderlabid\Finance\Events\Account\AccountCreating;
use Thunderlabid\Finance\Events\Account\AccountUpdated;
use Thunderlabid\Finance\Events\Account\AccountUpdating;
use Thunderlabid\Finance\Events\Account\AccountDeleted;
use Thunderlabid\Finance\Events\Account\AccountDeleting;
use Thunderlabid\Finance\Events\Account\AccountRestored;
use Thunderlabid\Finance\Events\Account\AccountRestoring;

class Account extends Model
{
	use SoftDeletes;

	protected $table 	= 'f_account';
	protected $fillable = ['kode_kantor', 'kode_akun', 'akun', 'is_pasiva'];
	protected $hidden 	= [];
	protected $appends	= [];

	protected $rules	= [];
	protected $errors;

	protected $dispatchesEvents = [
        'created' 	=> AccountCreated::class,
        'creating' 	=> AccountCreating::class,
        'updated' 	=> AccountUpdated::class,
        'updating' 	=> AccountUpdating::class,
        'deleted' 	=> AccountDeleted::class,
        'deleting' 	=> AccountDeleting::class,
        'restoring' => AccountRestoring::class,
        'restored' 	=> AccountRestored::class,
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
	public function coas(){
		return $this->hasMany(COA::class, 'kode_akun', 'kode_akun');
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
		$rules['kode_kantor'] 		= ['required', 'string'];
		$rules['akun'] 				= ['required', 'string'];
		$rules['kode_akun'] 		= ['required', 'string'];
		$rules['is_pasiva'] 		= ['required', 'boolean'];

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
