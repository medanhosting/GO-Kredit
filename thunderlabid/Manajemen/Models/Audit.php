<?php

namespace Thunderlabid\Manajemen\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

use Validator;

////////////
// EVENTS //
////////////
use Thunderlabid\Manajemen\Events\Audit\AuditCreated;
use Thunderlabid\Manajemen\Events\Audit\AuditCreating;
use Thunderlabid\Manajemen\Events\Audit\AuditUpdated;
use Thunderlabid\Manajemen\Events\Audit\AuditUpdating;
use Thunderlabid\Manajemen\Events\Audit\AuditDeleted;
use Thunderlabid\Manajemen\Events\Audit\AuditDeleting;
use Thunderlabid\Manajemen\Events\Audit\AuditRestored;
use Thunderlabid\Manajemen\Events\Audit\AuditRestoring;

use App\Service\Traits\WaktuTrait;
use App\Service\Traits\TanggalTrait;

class Audit extends Model
{
	use WaktuTrait;
	use TanggalTrait;
	use SoftDeletes;

	protected $table 	= 'm_audit';
	protected $fillable = ['tanggal', 'kode_kantor', 'domain', 'data_lama', 'data_perubahan', 'data_baru', 'karyawan'];
	protected $hidden 	= [];
	protected $appends	= [];

	protected $rules	= [];
	protected $errors;

	protected $dispatchesEvents = [
        'created' 	=> AuditCreated::class,
        'creating' 	=> AuditCreating::class,
        'updated' 	=> AuditUpdated::class,
        'updating' 	=> AuditUpdating::class,
        'deleted' 	=> AuditDeleted::class,
        'deleting' 	=> AuditDeleting::class,
        'restoring' => AuditRestoring::class,
        'restored' 	=> AuditRestored::class,
    ];

	// ------------------------------------------------------------------------------------------------------------
	// CONSTRUCT
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// BOOT
	// ------------------------------------------------------------------------------------------------------------
	
	// ------------------------------------------------------------------------------------------------------------
	// RELATION
	// ------------------------------------------------------------------------------------------------------------

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
		$this->attributes['tanggal']	= $this->formatDateTimeFrom($variable);
	}
	
	public function setDataBaruAttribute($variable)
	{
		$this->attributes['data_baru']	= json_encode($variable);
	}
	public function setDataPerubahanAttribute($variable)
	{
		$this->attributes['data_perubahan']		= json_encode($variable);
	}
	public function setDataLamaAttribute($variable)
	{
		$this->attributes['data_lama']	= json_encode($variable);
	}

	public function setKaryawanAttribute($variable)
	{
		$this->attributes['karyawan']	= json_encode($variable);
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
		$rules['kode_kantor'] 		= ['required', 'string'];
		$rules['domain'] 			= ['required', 'string'];
		$rules['karyawan'] 			= ['required', 'string'];

		//////////////
		// Validate //
		//////////////
		$validator = Validator::make($this->attributes, $rules);
		if ($validator->fails())
		{
			$this->errors = $validator->messages();
			return false;
		}
		return true;
	}

	public function getErrorsAttribute()
	{
		return $this->errors;
	}

	public function getTanggalAttribute($variable)
	{
		return $this->formatDateTo($this->attributes['tanggal']);
	}

	public function getKaryawanAttribute($variable)
	{
		return json_decode($this->attributes['karyawan'], true);
	}


	public function getDataBaruAttribute($variable)
	{
		return json_decode($this->attributes['data_baru'], true);
	}


	public function getDataPerubahanAttribute($variable)
	{
		return json_decode($this->attributes['data_perubahan'], true);
	}

	public function getDataLamaAttribute($variable)
	{
		return json_decode($this->attributes['data_lama'], true);
	}

}
