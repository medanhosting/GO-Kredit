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
use Thunderlabid\Socialmedia\Events\Igstatistics\IGStatisticsCreated;
use Thunderlabid\Socialmedia\Events\Igstatistics\IGStatisticsCreating;
use Thunderlabid\Socialmedia\Events\Igstatistics\IGStatisticsUpdated;
use Thunderlabid\Socialmedia\Events\Igstatistics\IGStatisticsUpdating;
use Thunderlabid\Socialmedia\Events\Igstatistics\IGStatisticsDeleted;
use Thunderlabid\Socialmedia\Events\Igstatistics\IGStatisticsDeleting;

class IGStatistics extends Model
{

	protected $table	= 'socialmedia_ig_statistics';
	protected $fillable	= ['account_id', 'followers', 'follows', 'media'];
	protected $hidden	= [];
	protected $rules	= [];
	protected $errors;

	protected $events = [
        'created' 	=> IGStatisticsCreated::class,
        'creating' 	=> IGStatisticsCreating::class,
        'updated' 	=> IGStatisticsUpdated::class,
        'updating' 	=> IGStatisticsUpdating::class,
        'deleted' 	=> IGStatisticsDeleted::class,
        'deleting' 	=> IGStatisticsDeleting::class,
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
	public function scopeOf($q, $v = null)
	{
		if (!$v) return $q;
		return $q->where('account_id', '=', $v);
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

			return $q->where('created_at', '>=', $date1)
				->where('created_at', '<=', $date2);
		}
		elseif ($date1)
		{
			return $q->where('created_at', '>=', $date1);
		}
		elseif ($date2)
		{
			return $q->where('created_at', '<=', $date2);
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
		$rules['account_id']		= ['required', 'exists:' . $this->account()->getRelated()->getTable() . ',id'];
		$rules['followers']			= ['required', 'numeric', 'min:0'];
		$rules['follows']			= ['required', 'numeric', 'min:0'];
		$rules['media']				= ['required', 'numeric', 'min:0'];

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
