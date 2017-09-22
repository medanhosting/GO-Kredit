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
use Thunderlabid\Survei\Events\Survei\SurveiCreated;
use Thunderlabid\Survei\Events\Survei\SurveiUpdating;
// use Thunderlabid\Survei\Events\Survei\SurveiUpdated;
// use Thunderlabid\Survei\Events\Survei\SurveiDeleting;
// use Thunderlabid\Survei\Events\Survei\SurveiDeleted;

use Thunderlabid\Survei\Traits\WaktuTrait;
use Thunderlabid\Pengajuan\Models\Pengajuan;

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
		'created' 	=> SurveiCreated::class,
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

	public function character()
	{
		return $this->hasOne(SurveiDetail::class, 'survei_id')->where('jenis', 'character');
	}

	public function condition()
	{
		return $this->hasOne(SurveiDetail::class, 'survei_id')->where('jenis', 'condition');
	}

	public function capacity()
	{
		return $this->hasOne(SurveiDetail::class, 'survei_id')->where('jenis', 'capacity');
	}

	public function capital()
	{
		return $this->hasOne(SurveiDetail::class, 'survei_id')->where('jenis', 'capital');
	}

	public function collateral()
	{
		return $this->hasMany(SurveiDetail::class, 'survei_id')->where('jenis', 'collateral');
	}

	public function jaminan_kendaraan()
	{
		return $this->hasMany(SurveiDetail::class, 'survei_id')->where('jenis', 'collateral')->where('dokumen_survei->collateral->jenis', 'bpkb');
	}

	public function jaminan_tanah_bangunan()
	{
		return $this->hasMany(SurveiDetail::class, 'survei_id')->where('jenis', 'collateral')->whereIn('dokumen_survei->collateral->jenis', ['shm', 'shgb']);
	}

	public function foto()
	{
		return $this->hasMany(SurveiFoto::class, 'survei_id');
	}

	public function pengajuan()
	{
		return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
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
