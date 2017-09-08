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
use Thunderlabid\Socialmedia\Events\Followers\FollowerCreated;
use Thunderlabid\Socialmedia\Events\Followers\FollowerCreating;
use Thunderlabid\Socialmedia\Events\Followers\FollowerUpdated;
use Thunderlabid\Socialmedia\Events\Followers\FollowerUpdating;
use Thunderlabid\Socialmedia\Events\Followers\FollowerDeleted;
use Thunderlabid\Socialmedia\Events\Followers\FollowerDeleting;

class Follower extends Model
{

	protected $table	= 'socialmedia_followers';
	protected $fillable	= ['user_id', 'user_name', 'user_data', 'follow_at', 'unfollow_at'];
	protected $hidden	= [];
	protected $dates	= ['follow_at', 'unfollow_at'];

	protected $rules	= [];
	protected $errors;

	protected $events = [
		'created' 	=> FollowerCreated::class,
		'creating' 	=> FollowerCreating::class,
		'updated' 	=> FollowerUpdated::class,
		'updating' 	=> FollowerUpdating::class,
		'deleted' 	=> FollowerDeleted::class,
		'deleting' 	=> FollowerDeleting::class,
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
	public function account()
	{
		return $this->belongsTo(Account::class, 'account_id');
	}

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------
	public function scopeUserId($q, $v)
	{
		return $q->where('user_id', '=', $v);
	}

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// ACCESSOR
	// ------------------------------------------------------------------------------------------------------------
	public function getIsDeletableAttribute()
	{
		return false;
	}

	public function getIsSavableAttribute()
	{
		//////////////////
		// Create Rules //
		//////////////////
		$rules['user_id']		= ['required', 'string'];
		$rules['user_name']		= ['required', 'string'];
		$rules['user_data']		= ['required', 'string'];
		$rules['follow_at']		= ['required', 'date'];
		$rules['unfollow_at'] 	= ['nullable', 'date', 'after:follow_at'];

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
