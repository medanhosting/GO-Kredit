<?php

namespace Thunderlabid\Manajemen\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Validator;
use Carbon\Carbon;

use Thunderlabid\Manajemen\Traits\WaktuTrait;

///////////////
// Exception //
///////////////
use Thunderlabid\Manajemen\Exceptions\AppException;

////////////
// EVENTS //
////////////
// use Thunderlabid\Manajemen\Events\PenempatanKaryawan\PenempatanKaryawanCreating;
// use Thunderlabid\Manajemen\Events\PenempatanKaryawan\PenempatanKaryawanCreated;
use Thunderlabid\Manajemen\Events\PenempatanKaryawan\PenempatanKaryawanSaving;
// use Thunderlabid\Manajemen\Events\PenempatanKaryawan\PenempatanKaryawanUpdating;
// use Thunderlabid\Manajemen\Events\PenempatanKaryawan\PenempatanKaryawanUpdated;
// use Thunderlabid\Manajemen\Events\PenempatanKaryawan\PenempatanKaryawanDeleting;
// use Thunderlabid\Manajemen\Events\PenempatanKaryawan\PenempatanKaryawanDeleted;

class PenempatanKaryawan extends Model
{
	use SoftDeletes;
	use WaktuTrait;

	protected $table	= 'm_penempatan_karyawan';
	protected $fillable	= ['kantor_id', 'orang_id', 'role', 'scopes', 'policies', 'tanggal_masuk', 'tanggal_keluar'];
	protected $hidden	= [];
	protected $dates	= [];
	protected $appends	= ['is_savable', 'is_deletable'];

	protected $rules	= [];
	protected $errors;
	protected $latest_analysis;

	protected $events = [
		// 'creating' 	=> PenempatanKaryawanCreating::class,
		// 'created' 	=> PenempatanKaryawanCreated::class,
		'saving' 		=> PenempatanKaryawanSaving::class,
		// 'saved' 		=> PenempatanKaryawanSaved::class,
		// 'updated' 	=> PenempatanKaryawanUpdated::class,
		// 'updating' 	=> PenempatanKaryawanUpdating::class,
		// 'deleted' 	=> PenempatanKaryawanDeleted::class,
		// 'deleting' 	=> PenempatanKaryawanDeleting::class,
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

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------

	public function scopeActive($query, Carbon $tanggal_aktif)
	{
		return $query->where(function($q)use($tanggal_aktif){$q->wherenull('tanggal_keluar')->orwhere('tanggal_keluar', '>', $tanggal_aktif->format('Y-m-d H:i:s'));});
	}

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setTanggalMasukAttribute($variable)
	{
		$this->attributes['tanggal_masuk']	= $this->formatDateTimeFrom($variable);
	}

	public function setTanggalKeluarAttribute($variable)
	{
		$this->attributes['tanggal_keluar']	= $this->formatDateTimeFrom($variable);
	}

	public function setScopesAttribute($variable)
	{
		$this->attributes['scopes']		= json_encode($variable);
	}

	public function setPoliciesAttribute($variable)
	{
		$this->attributes['policies']	= json_encode($variable);
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
		$rules['kantor_id']	= ['required'];
		$rules['orang_id']	= ['required'];
		$rules['role']		= ['required'];
		$rules['scopes']	= ['required', 'json'];
		$rules['policies']	= ['json'];
		$rules['tanggal_masuk']		= ['required','date_format:"Y-m-d H:i:s"'];
		$rules['tanggal_keluar']	= ['date_format:"Y-m-d H:i:s"'];

		//////////////
		// Validate //
		//////////////
		$validator = Validator::make($this->attributes, $rules);
		if ($validator->fails())
		{
			$this->errors = $validator->messages();
			return false;
		}

		//check possibility of duplicate kantor
		$id 			= $this->id;
		if(is_null($this->id))
		{
			$id 		= 0;
		}
		$penempatan 	= PenempatanKaryawan::where('kantor_id', $this->attributes['kantor_id'])->where('orang_id', $this->attributes['orang_id'])->where('id', '<>', $id)->active(Carbon::parse($this->attributes['tanggal_masuk']));

		if(!is_null($this->attributes['tanggal_keluar']))
		{
			$penempatan = $penempatan->active(Carbon::parse($this->attributes['tanggal_keluar']));
		}
		$penempatan 	= $penempatan->first();
		
		if($penempatan)
		{
			$this->errors = 'Karyawan sudah terdaftar di cabang ini pada tanggal rentang waktu tersebut';
			return false;
		}
		else
		{
			$this->errors = null;
			return true;
		}
	}

	public function getPoliciesAttribute()
	{
		return json_decode($this->attributes['policies'], true);
	}

	public function getScopesAttribute()
	{
		return json_decode($this->attributes['scopes'], true);
	}

	public function getTanggalMasukAttribute($value)
	{
		return $this->formatDateTimeTo($value);
	}

	public function getTanggalKeluarAttribute($value)
	{
		return $this->formatDateTimeTo($value);
	}

	public function getErrorsAttribute()
	{
		return $this->errors;
	}
}
