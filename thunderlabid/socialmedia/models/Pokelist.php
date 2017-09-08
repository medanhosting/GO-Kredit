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
use Thunderlabid\Socialmedia\Events\PokelistCreated;
use Thunderlabid\Socialmedia\Events\PokelistUpdated;
use Thunderlabid\Socialmedia\Events\PokelistDeleted;

class Pokelist extends Model
{

	protected $table	= 'socialmedia_pokelist';
	protected $fillable	= ['user_id', 'fullname', 'username', 'is_active'];
	protected $hidden	= [];
	protected $dates	= ['executed_at'];

	protected $rules	= [];
	protected $errors;

	protected $attributes = [
		'is_active' 		=> 0
	];

	// ------------------------------------------------------------------------------------------------------------
	// CONSTRUCT
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// BOOT
	// -----------------------------------------------------------------------------------------------------------
	public static function boot()
	{
		parent::boot();

		////////////////
		// VALIDATION //
		////////////////
		Self::saving(function($model)	{ if (!$model->is_savable) throw new AppException($model->errors, AppException::DATA_VALIDATION); });
		Self::deleting(function($model)	{ if (!$model->is_deletable) throw new AppException($model->errors, AppException::DATA_VALIDATION); });

		///////////
		// EVENT //
		///////////
		Self::created(function($model)	{ event(new PokelistCreated($model)); });
		Self::updated(function($model)	{ event(new PokelistUpdated($model)); });
		Self::deleted(function($model)	{ event(new PokelistDeleted($model)); });
	}
	
	// ------------------------------------------------------------------------------------------------------------
	// RELATION
	// ------------------------------------------------------------------------------------------------------------
	public function account()
	{
		return $this->belongsTo(Account::class, 'account_id');
	}

	public function watchlists()
	{
		return $this->belongsToMany(Watchlist::class, 'socialmedia_pokelist_watchlist')->withTimestamps();
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
		$rules['user_id']			= ['required'];
		$rules['fullname']			= ['nullable', 'string'];
		$rules['username']			= ['required', 'string'];
		$rules['is_active']			= ['required', 'boolean'];

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
