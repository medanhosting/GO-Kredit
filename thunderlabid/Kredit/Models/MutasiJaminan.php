<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

use Validator;

use App\Service\Traits\WaktuTrait;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanCreated;
use Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanCreating;
use Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanUpdated;
use Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanUpdating;
use Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanDeleted;
use Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanDeleting;
use Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanRestored;
use Thunderlabid\Kredit\Events\MutasiJaminan\MutasiJaminanRestoring;

class MutasiJaminan extends Model
{
	use WaktuTrait;

	protected $table 	= 'k_mutasi_jaminan';
	protected $fillable = ['nomor_kredit', 'tanggal', 'tag', 'description', 'documents'];
	protected $hidden 	= [];
	protected $appends	= [];
	protected $rules	= [];
	protected $errors;
	protected $events = [
        'created' 	=> MutasiJaminanCreated::class,
        'creating' 	=> MutasiJaminanCreating::class,
        'updated' 	=> MutasiJaminanUpdated::class,
        'updating' 	=> MutasiJaminanUpdating::class,
        'deleted' 	=> MutasiJaminanDeleted::class,
        'deleting' 	=> MutasiJaminanDeleting::class,
        'restoring' => MutasiJaminanRestoring::class,
        'restored' 	=> MutasiJaminanRestored::class,
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
	public function kredit(){
		return $this->belongsto(Aktif::class, 'nomor_kredit', 'nomor_kredit');
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
		$this->attributes['tanggal']	= $this->formatDateTimeFrom($variable);
	}

	public function setDocumentsAttribute($variable)
	{
		$this->attributes['documents']	= json_encode($variable);
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
		$rules['nomor_kredit'] 		= ['required', 'string'];
		$rules['tanggal'] 			= ['required', 'date_format:"Y-m-d H:i:s"'];
		$rules['tag'] 				= ['required', 'string'];

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

	public function getDocumentsAttribute($variable)
	{
		return json_decode($this->attributes['documents'], true);
	}
}
