<?php

namespace Thunderlabid\Finance\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Validator;

////////////
// EVENTS //
////////////
use Thunderlabid\Finance\Events\COA\COACreated;
use Thunderlabid\Finance\Events\COA\COACreating;
use Thunderlabid\Finance\Events\COA\COAUpdated;
use Thunderlabid\Finance\Events\COA\COAUpdating;
use Thunderlabid\Finance\Events\COA\COADeleted;
use Thunderlabid\Finance\Events\COA\COADeleting;
use Thunderlabid\Finance\Events\COA\COARestored;
use Thunderlabid\Finance\Events\COA\COARestoring;

class COA extends Model
{
	use SoftDeletes;

	protected $table 	= 'f_coa';
	protected $fillable = ['transaction_detail_id', 'akun_id'];
	protected $hidden 	= [];
	protected $appends	= [];

	protected $rules	= [];
	protected $errors;

	protected $dispatchesEvents = [
        'created' 	=> COACreated::class,
        'creating' 	=> COACreating::class,
        'updated' 	=> COAUpdated::class,
        'updating' 	=> COAUpdating::class,
        'deleted' 	=> COADeleted::class,
        'deleting' 	=> COADeleting::class,
        'restoring' => COARestoring::class,
        'restored' 	=> COARestored::class,
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
	public function account()
	{
		return $this->belongsTo(Account::class, 'akun_id', 'akun_id');
	}

	public function detail(){
		return $this->hasOne(TransactionDetail::class, 'id', 'transaction_detail_id');
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
		$rules['transaction_detail_id'] = ['required'];
		$rules['akun_id'] 				= ['required', 'numeric'];

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