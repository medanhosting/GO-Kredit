<?php

namespace Thunderlabid\Socialmedia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Validator;

///////////////
// Exception //
///////////////
use Thunderlabid\Socialmedia\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Socialmedia\Events\Accounts\AccountCreated;
use Thunderlabid\Socialmedia\Events\Accounts\AccountCreating;
use Thunderlabid\Socialmedia\Events\Accounts\AccountUpdated;
use Thunderlabid\Socialmedia\Events\Accounts\AccountUpdating;
use Thunderlabid\Socialmedia\Events\Accounts\AccountDeleted;
use Thunderlabid\Socialmedia\Events\Accounts\AccountDeleting;

class Account extends Model
{
	use SoftDeletes;

	protected $table	= 'socialmedia_accounts';
	protected $fillable	= ['name', 'type', 'owner', 'data'];
	protected $hidden	= [];
	protected $dates	= [];

	protected $rules	= [];
	protected $errors;
	
	protected $latest_analysis;

	public static $types= ['instagram'];

	protected $events = [
        'created' 	=> AccountCreated::class,
        'creating' 	=> AccountCreating::class,
        'updated' 	=> AccountUpdated::class,
        'updating' 	=> AccountUpdating::class,
        'deleted' 	=> AccountDeleted::class,
        'deleting' 	=> AccountDeleting::class,
    ];
	
	// ------------------------------------------------------------------------------------------------------------
	// CONSTRUCT
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// BOOT
	// -----------------------------------------------------------------------------------------------------------
	
	// ------------------------------------------------------------------------------------------------------------
	// RELATION
	// ------------------------------------------------------------------------------------------------------------
	public function followers()
	{
		return $this->hasMany(Follower::class, 'account_id');
	}

	public function watchlists()
	{
		return $this->hasMany(Watchlist::class, 'account_id');
	}

	public function pokelists()
	{
		return $this->hasMany(Pokelist::class, 'account_id');
	}

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------
	public function scopeName($q, $v = null)
	{
		if (!$v) return $q;
		return $q->where('name', 'like', '%'.$v.'%');
	}

	public function scopeOwner($q, $v = null)
	{
		if (!$v) return $q;
		return $q->where('owner', '=', $v);
	}

	public function scopeType($q, $v = null)
	{
		if (!$v) return $q;
		return $q->where('type', '=', $v);
	}

	public function scopeActive($q)
	{
		return $q->whereNotNull('access_token');
	}

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// ACCESSOR
	// ------------------------------------------------------------------------------------------------------------
	public function getIsActiveAttribute()
	{
		return strlen($this->access_token) ? true : false;
	}

	public function getIsInstagramAttribute()
	{
		return $this->attributes['type'] == 'instagram';
	}

	public function GetLatestAnalysisAttribute()
	{
		if (!$this->latest_analysis)
		{
			$this->latest_analysis = $this->analysis()->newQuery()->latest()->first();
		}

		return $this->latest_analysis;
	}

	public function getIsDeletableAttribute()
	{
		return true;
	}

	public function getIsSavableAttribute()
	{
		//////////////////
		// Create Rules //
		//////////////////
		$rules['name']			= ['required', 'string'];
		$rules['owner'] 		= ['required', 'integer', 'min:1'];
		$rules['type'] 			= ['required', 'in:' . implode(',',SELF::$types)];
		$rules['access_token'] 	= ['nullable', 'string'];
		$rules['data'] 			= ['nullable', 'string'];

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
