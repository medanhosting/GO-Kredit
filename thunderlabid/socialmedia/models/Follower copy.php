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
use Thunderlabid\Socialmedia\Events\AnalysisCreated;
use Thunderlabid\Socialmedia\Events\AnalysisUpdated;
use Thunderlabid\Socialmedia\Events\AnalysisDeleted;

class Follower extends Model
{

	protected $table	= 'socialmedia_followers';
	protected $fillable	= ['user_id', 'user_name', 'follow_at', 'unfollow_at'];
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
	public static function boot()
	{
		parent::boot();

		////////////////
		// VALIDATION //
		////////////////
		Self::saving(function($model)	{ if (!$model->is_savable) throw new AppException($model->errors, AppException::DATA_VALIDATION); });
		Self::deleting(function($model)	{ if (!$model->is_deletable) throw new AppException($model->errors, AppException::DATA_VALIDATION); });

	}
	
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
	public function compare(Analysis $compare_to)
	{
		if ($this->created_at->gt($compare_to->created_at))
		{
			$prev_analysis = $compare_to;
			$new_analysis = $this;
		}
		else
		{
			$prev_analysis = $this;
			$new_analysis = $compare_to;	
		}

        $unfollower_list 	= $prev_analysis->follower_list->filter(function($item) use ($new_analysis) { return $new_analysis->follower_list->where('id', $item->id)->count() == 0; })->toArray();
        $new_follower_list 	= $new_analysis->follower_list->filter(function($item) use ($prev_analysis) { return $prev_analysis->follower_list->where('id', $item->id)->count() == 0; })->toArray();

        return [
        	'unfollower' 	=> $unfollower_list,
        	'new_follower' 	=> $new_follower_list,
        ];
	}

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------
	public function scopeToday($q)
	{
		return $q->where('created_at', '>=', \Carbon\Carbon::now()->startOfDay())
				->where('created_at', '<=', \Carbon\Carbon::now()->endOfDay());
	}

	public function scopeBetween($q, $date1, $date2)
	{
		return $q->where('created_at', '>=', $date1->gte($date2) ? $date2 : $date1)
				->where('created_at', '<=', $date1->gte($date2) ? $date1 : $date2);
	}

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setFollowerListAttribute($followers = [])
	{
		$data = json_decode($this->attributes['data']);
		$data->followers->list = $followers;
		$this->attributes['data'] = json_encode($data);
	}	

	public function setUnfollowerListAttribute($unfollowers = [])
	{
		$data = json_decode($this->attributes['data']);
		$data->unfollowers->list = $unfollowers;
		$this->attributes['data'] = json_encode($data);
	}	

	public function setNewFollowerListAttribute($followers = [])
	{
		$data = json_decode($this->attributes['data']);
		$data->new_followers->list = $followers;
		$this->attributes['data'] = json_encode($data);
	}	

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
		$rules['followers']		= ['required', 'numeric', 'min:0'];
		$rules['data'] 			= ['required', 'string'];

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

	public function getFollowerListAttribute()
	{
		$data = json_decode($this->data);
		$followers = collect();
		foreach ($data->followers->list as $follower)
		{
			$followers->push($follower);
		}

		return $followers;
	}

	public function getUnfollowerListAttribute()
	{
		$data = json_decode($this->data);
		$unfollowers = collect();
		foreach ($data->unfollowers->list as $follower)
		{
			$unfollowers->push($follower);
		}

		return $unfollowers;
	}

	public function getNewFollowerListAttribute()
	{
		$data = json_decode($this->data);
		$new_followers = collect();
		foreach ($data->new_followers->list as $follower)
		{
			$new_followers->push($follower);
		}

		return $new_followers;
	}
}
