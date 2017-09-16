<?php

namespace Thunderlabid\Log\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Validator;

///////////////
// Exception //
///////////////
use Thunderlabid\Log\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Log\Events\SHGB\SHGBCreating;
// use Thunderlabid\Log\Events\SHGB\SHGBCreated;
use Thunderlabid\Log\Events\SHGB\SHGBUpdating;
// use Thunderlabid\Log\Events\SHGB\SHGBUpdated;
// use Thunderlabid\Log\Events\SHGB\SHGBDeleting;
// use Thunderlabid\Log\Events\SHGB\SHGBDeleted;

use Thunderlabid\Log\Traits\WaktuTrait;

class SHGB extends Model
{
	use SoftDeletes;
	use WaktuTrait;

	protected $table	= 'l_shgb';
	protected $fillable	= ['parent_id', 'tipe', 'nomor_sertifikat', 'atas_nama', 'luas_tanah', 'luas_bangunan', 'alamat', 'masa_berlaku_sertifikat'];

	protected $hidden	= [];
	protected $dates	= [];
	protected $rules	= [];
	protected $errors;
	protected $latest_analysis;

	protected $events 		= [
		'creating' 	=> SHGBCreating::class,
		'updating' 	=> SHGBUpdating::class,
		// 'deleted' 	=> BPKBDeleted::class,
		// 'deleting' 	=> BPKBDeleting::class,
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
	// public function log()
	// {
	// 	return $this->hasMany(BPKBLog::class, 'BPKB_id');
	// }
	
	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setAlamatAttribute($variable)
	{
		$this->attributes['alamat']		= json_encode($variable);
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
		$rules['tipe']				= ['required', 'max:255', 'in:tanah,tanah_dan_bangunan'];
		$rules['nomor_sertifikat']	= ['required', 'max:255'];
		$rules['atas_nama']			= ['required', 'max:255'];
		$rules['luas_tanah']		= ['required', 'numeric'];
		$rules['luas_bangunan']		= ['required_if:tipe,tanah_dan_bangunan', 'numeric'];
		$rules['alamat']			= ['required', 'array'];
		$rules['masa_berlaku_sertifikat']	= ['required', 'max:255', 'date_format:"Y"'];

		//////////////
		// Validate //
		//////////////
		$data 				= $this->attributes;
		$data['alamat']		= json_decode($data['alamat'], true);

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

	public function getAlamatAttribute($variable)
	{
		return json_decode($this->attributes['alamat'], true);
	}

	public function getErrorsAttribute()
	{
		return $this->errors;
	}
}
