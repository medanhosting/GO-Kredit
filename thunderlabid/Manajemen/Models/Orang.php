<?php

namespace Thunderlabid\Manajemen\Models;

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
use Thunderlabid\Manajemen\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Manajemen\Events\Orang\OrangCreated;
use Thunderlabid\Manajemen\Events\Orang\OrangCreating;
use Thunderlabid\Manajemen\Events\Orang\OrangUpdated;
use Thunderlabid\Manajemen\Events\Orang\OrangUpdating;
use Thunderlabid\Manajemen\Events\Orang\OrangDeleted;
use Thunderlabid\Manajemen\Events\Orang\OrangDeleting;

class Orang extends Authenticatable
{
	use Notifiable, SoftDeletes;

	protected $table 	= 'm_orang';
	protected $fillable = [ 'nip', 'nama', 'email', 'password', 'alamat', 'telepon'];
	protected $hidden 	= ['password', 'remember_token',];
	protected $dates	= ['deleted_at'];
	protected $appends	= ['is_savable', 'is_deletable'];
	protected $rules	= [];
	protected $errors;

	protected $events = [
        'created' 	=> OrangCreated::class,
        'creating' 	=> OrangCreating::class,
        'updated' 	=> OrangUpdated::class,
        'updating' 	=> OrangUpdating::class,
        'deleted' 	=> OrangDeleted::class,
        'deleting' 	=> OrangDeleting::class,
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
		$rules['nama'] 			= ['required', 'string'];
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
