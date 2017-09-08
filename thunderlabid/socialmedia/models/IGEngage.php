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
use Thunderlabid\Socialmedia\Events\IGEngage\IGEngageCreated;
use Thunderlabid\Socialmedia\Events\IGEngage\IGEngageCreating;
use Thunderlabid\Socialmedia\Events\IGEngage\IGEngageUpdated;
use Thunderlabid\Socialmedia\Events\IGEngage\IGEngageUpdating;
use Thunderlabid\Socialmedia\Events\IGEngage\IGEngageDeleted;
use Thunderlabid\Socialmedia\Events\IGEngage\IGEngageDeleting;

class IGEngage extends Model
{

	protected $table	= 'socialmedia_ig_engagements';
	protected $fillable	= ['account_id', 'type', 'value', 'executed_at', 'is_active'];
	protected $hidden	= [];
	protected $dates	= ['executed_at'];
	protected $errors;

	protected $attributes = [
		'type' 				=> null,
		'value' 			=> null,
		'executed_at' 		=> null,
		'is_active' 		=> 1
	];

	public static $type	= [
		'ig-tag-mediaowner' 		=> 'Engage people who post in certain tag',
		'ig-tag-medialikers'		=> 'Engage people who like media posted in certain tag',
		'ig-location-mediaowner'	=> 'Engage people who post in certain location',
		'ig-account-owner'			=> 'Engage certain account',
		'ig-account-followers'		=> 'Engage people who followes certain account',
	];

	protected $events = [
        'created' 	=> IGEngageCreated::class,
        'creating' 	=> IGEngageCreating::class,
        'updated' 	=> IGEngageUpdated::class,
        'updating' 	=> IGEngageUpdating::class,
        'deleted' 	=> IGEngageDeleted::class,
        'deleting' 	=> IGEngageDeleting::class,
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
	public function activate()
	{
		$this->attributes['is_active'] = true;
		$this->save();
	}

	public function deactivate()
	{
		$this->attributes['is_active'] = false;
		$this->save();
	}
	
	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------
	public function scopeType($q, $v = null)
	{
		if (!$v) return $q;
		return $q->where('type', 'like', $v);
	}

	public function scopeOf($q, $v = null)
	{
		if (!$v) return $q;
		return $q->where('account_id', '=', $v);
	}

	public function scopeActive($q)
	{
		return $q->where('is_active', '=', true);
	}

	public function scopeInactive($q)
	{
		return $q->where('is_active', '=', false);
	}

	public function scopeSearch($q, $v = null)
	{
		if (!$v) return $q;
		return $q->where('value', 'like', '%'.$v.'%');
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
		$rules['type']			= ['required', 'in:' . implode(',', array_keys(Self::$type))];
		$rules['value']			= ['required', 'string'];
		$rules['executed_at']	= ['nullable', 'date'];
		$rules['is_active']		= ['required', 'boolean'];

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
