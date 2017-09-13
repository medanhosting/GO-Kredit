<?php

namespace Thunderlabid\Survei\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Validator, DB;

///////////////
// Exception //
///////////////
use Thunderlabid\Survei\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Survei\Events\Survei\SurveiCreating;
// use Thunderlabid\Survei\Events\Survei\SurveiCreated;
use Thunderlabid\Survei\Events\Survei\SurveiUpdating;
// use Thunderlabid\Survei\Events\Survei\SurveiUpdated;
// use Thunderlabid\Survei\Events\Survei\SurveiDeleting;
// use Thunderlabid\Survei\Events\Survei\SurveiDeleted;

use Thunderlabid\Survei\Traits\WaktuTrait;

class Survei extends Model
{
	use SoftDeletes;
	use WaktuTrait;

	protected $table	= 's_survei';
	protected $fillable	= ['tanggal', 'surveyor', 'pengajuan_id'];
	protected $hidden	= [];
	protected $dates	= [];

	protected $rules	= [];
	protected $errors;
	protected $latest_analysis;
    
	protected $events 	= [
		'creating' 	=> SurveiCreating::class,
		// 'created' 	=> SurveiCreated::class,
		'updating' 	=> SurveiUpdating::class,
		// 'updated' 	=> SurveiUpdated::class,
		// 'deleted' 	=> SurveiDeleted::class,
		// 'deleting' 	=> SurveiDeleting::class,
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
	public function details()
	{
		return $this->hasMany(SurveiDetail::class, 'survei_id');
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
	public function setTanggalAttribute($variable)
	{
		$this->attributes['tanggal']  	= $this->formatDateTimeFrom($variable);
	}
	
	public function setSurveyorAttribute($variable)
	{
		$this->attributes['surveyor']	= json_encode($variable);
	}

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
		$rules['tanggal']			= ['required', 'date_format:"Y-m-d"'];
		$rules['surveyor']['nip']	= ['required'];
		$rules['surveyor']['nama']	= ['required'];
		$rules['pengajuan_id']		= ['required', 'exists:p_pengajuan,id'];

		//////////////
		// Validate //
		//////////////
		$data 				= $this->attributes;
		$data['surveyor'] 	= json_decode($data['surveyor'], true);

		$validator = Validator::make($data, $rules);
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

	public function getTanggalAttribute($variable)
	{
		return $this->formatDateTimeTo($this->attributes['tanggal']);
	}

	public function getSurveyorAttribute($variable)
	{
		return json_decode($this->attributes['surveyor'], true);
	}

	public function getErrorsAttribute()
	{
		return $this->errors;
	}
}
