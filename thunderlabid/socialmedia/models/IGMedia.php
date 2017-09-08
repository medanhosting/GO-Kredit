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
use Thunderlabid\Socialmedia\Events\IGMedia\IGMediaCreated;
use Thunderlabid\Socialmedia\Events\IGMedia\IGMediaCreating;
use Thunderlabid\Socialmedia\Events\IGMedia\IGMediaUpdated;
use Thunderlabid\Socialmedia\Events\IGMedia\IGMediaUpdating;
use Thunderlabid\Socialmedia\Events\IGMedia\IGMediaDeleted;
use Thunderlabid\Socialmedia\Events\IGMedia\IGMediaDeleting;

class IGMedia extends Model
{

	protected $table	= 'socialmedia_ig_media';
	protected $fillable	= ['account_id', 'ig_id', 'type', 'url', 'tags', 'caption', 'link', 'likes', 'comments', 'posted_at'];
	protected $hidden	= [];
	protected $dates	= ['posted_at'];
	protected $errors;

	protected $attributes = [
		'ig_id'			=> '',
		'type'			=> '',
		'url'			=> '',
		'tags'			=> '',
		'caption'		=> '',
		'link'			=> '',
		'likes'			=> 0,
		'comments'		=> 0,
		'posted_at'		=> '',
	];

	protected $events = [
        'created' 	=> IGMediaCreated::class,
        'creating' 	=> IGMediaCreating::class,
        'updated' 	=> IGMediaUpdated::class,
        'updating' 	=> IGMediaUpdating::class,
        'deleted' 	=> IGMediaDeleted::class,
        'deleting' 	=> IGMediaDeleting::class,
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
	public function scopeIGId($q, $v = null)
	{
		if (!$v) return $q;
		return $q->where('ig_id', '=', $v);
	}

	public function scopeOf($q, $v = null)
	{
		if (!$v) return $q;
		return $q->where('account_id', '=', $v);
	}

	public function scopeType($q, $v = null)
	{
		if (!$v) return $q;
		return $q->where('type', '=', $v);
	}

	public function scopeBetween($q, \Carbon\Carbon $date1 = null, \Carbon\Carbon $date2 = null)
	{
		if ($date1 && $date2)
		{
			if ($date1->gt($date2))
			{
				$x = $date1; 
				$date1 = $date2;
				$date2 = $x;
			}

			return $q->where('posted_at', '>=', $date1)
				->where('posted_at', '<=', $date2);
		}
		elseif ($date1)
		{
			return $q->where('posted_at', '>=', $date1);
		}
		elseif ($date2)
		{
			return $q->where('posted_at', '<=', $date2);
		}
		else
		{
			return $q;
		}
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
		$rules['ig_id'] 		= ['required', 'string'];
		$rules['type'] 			= ['required', 'string'];
		$rules['url'] 			= ['required', 'string'];
		$rules['tags'] 			= ['nullable', 'string'];
		$rules['caption'] 		= ['nullable'];
		$rules['link'] 			= ['required', 'string'];
		$rules['likes'] 		= ['required', 'numeric', 'min:0'];
		$rules['comments'] 		= ['required', 'numeric', 'min:0'];
		$rules['posted_at'] 	= ['required', 'date'];

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
