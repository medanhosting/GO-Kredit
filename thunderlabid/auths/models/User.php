<?php

namespace Thunderlabid\Auths\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Validator;
use Exception;
use Hash;

///////////////
// Exception //
///////////////
use Thunderlabid\Auths\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Auths\Events\Users\UserCreated;
use Thunderlabid\Auths\Events\Users\UserCreating;
use Thunderlabid\Auths\Events\Users\UserUpdated;
use Thunderlabid\Auths\Events\Users\UserUpdating;
use Thunderlabid\Auths\Events\Users\UserDeleted;
use Thunderlabid\Auths\Events\Users\UserDeleting;

class User extends Authenticatable
{
	use Notifiable, SoftDeletes;

	protected $table 	= 'auth_users';
	protected $fillable = ['name', 'email', 'password'];
	protected $hidden 	= ['password', 'remember_token',];
	protected $dates	= ['deleted_at'];
	protected $appends	= ['is_savable', 'is_deletable'];

	protected $rules	= [];
	protected $errors;

	protected $events = [
        'created' 	=> UserCreated::class,
        'creating' 	=> UserCreating::class,
        'updated' 	=> UserUpdated::class,
        'updating' 	=> UserUpdating::class,
        'deleted' 	=> UserDeleted::class,
        'deleting' 	=> UserDeleting::class,
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
	public function scopeEmail($q, $v = null)
	{
		if (!$v) return $q;
		return $q->where('email', 'like', $v);
	}

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
		$rules['name'] 			= ['required', 'string'];
		$rules['email'] 		= ['required', 'email', Rule::unique($this->table)->ignore($this->id)];
		$rules['password']		= ['required', 'min:6', 'string'];

		//////////////
		// Validate //
		//////////////
		$validator = Validator::make($this->attributes, $rules);
		if ($validator->fails())
		{
			$this->errors = $validator->messages();
			return false;
		}
		else
		{
			$this->errors = null;
			return true;
		}
	}

	public function getErrorsAttribute()
	{
		return $this->errors;
	}
}
