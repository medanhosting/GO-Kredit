<?php

namespace Thunderlabid\Finance\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Validator;
use App\Service\Traits\WaktuTrait;
use App\Service\Traits\TanggalTrait;

////////////
// EVENTS //
////////////
use Thunderlabid\Finance\Events\CetakNotaBayar\CetakNotaBayarCreated;
use Thunderlabid\Finance\Events\CetakNotaBayar\CetakNotaBayarCreating;
use Thunderlabid\Finance\Events\CetakNotaBayar\CetakNotaBayarUpdated;
use Thunderlabid\Finance\Events\CetakNotaBayar\CetakNotaBayarUpdating;
use Thunderlabid\Finance\Events\CetakNotaBayar\CetakNotaBayarDeleted;
use Thunderlabid\Finance\Events\CetakNotaBayar\CetakNotaBayarDeleting;
use Thunderlabid\Finance\Events\CetakNotaBayar\CetakNotaBayarRestored;
use Thunderlabid\Finance\Events\CetakNotaBayar\CetakNotaBayarRestoring;

class CetakNotaBayar extends Model
{
	use WaktuTrait;
	use TanggalTrait;
	use SoftDeletes;
	
	protected $table 	= 'f_cetak_nota_bayar';
	protected $fillable = ['nomor_faktur', 'tanggal', 'karyawan'];
	protected $hidden 	= [];
	protected $appends	= [];

	protected $rules	= [];
	protected $errors;

	protected $events = [
		'created' 	=> CetakNotaBayarCreated::class,
		'creating' 	=> CetakNotaBayarCreating::class,
		'updated' 	=> CetakNotaBayarUpdated::class,
		'updating' 	=> CetakNotaBayarUpdating::class,
		'deleted' 	=> CetakNotaBayarDeleted::class,
		'deleting' 	=> CetakNotaBayarDeleting::class,
		'restoring' => CetakNotaBayarRestoring::class,
		'restored' 	=> CetakNotaBayarRestored::class,
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
	public function notabayar(){
		return $this->belongsto(NotaBayar::class, 'nomor_faktur', 'nomor_faktur');
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
		$this->attributes['tanggal']		= $this->formatDateTimeFrom($variable);
	}

	public function setKaryawanAttribute($variable)
	{
		$this->attributes['karyawan']		= json_encode($variable);
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
		$rules['nomor_faktur'] 		= ['required', 'string'];
		$rules['tanggal'] 			= ['required', 'date_format:"Y-m-d H:i:s"'];

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
		return $this->formatDateTimeTo($this->attributes['tanggal']);
	}

	public function getHariAttribute($variable)
	{
		return $this->formatDateTo($this->attributes['tanggal']);
	}

	public function getKaryawanAttribute($variable)
	{
		return json_decode($this->attributes['karyawan'], true);
	}
}
