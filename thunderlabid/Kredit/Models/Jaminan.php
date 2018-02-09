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
use Thunderlabid\Kredit\Events\Jaminan\JaminanCreated;
use Thunderlabid\Kredit\Events\Jaminan\JaminanCreating;
use Thunderlabid\Kredit\Events\Jaminan\JaminanUpdated;
use Thunderlabid\Kredit\Events\Jaminan\JaminanUpdating;
use Thunderlabid\Kredit\Events\Jaminan\JaminanDeleted;
use Thunderlabid\Kredit\Events\Jaminan\JaminanDeleting;
use Thunderlabid\Kredit\Events\Jaminan\JaminanRestored;
use Thunderlabid\Kredit\Events\Jaminan\JaminanRestoring;

class Jaminan extends Model
{
	use WaktuTrait;

	protected $table 	= 'k_jaminan';
	protected $fillable = ['nomor_kredit', 'nomor_jaminan', 'kategori', 'dokumen'];
	protected $hidden 	= [];
	protected $appends	= ['possible_action'];
	protected $rules	= [];
	protected $errors;
	protected $events = [
        'created' 	=> JaminanCreated::class,
        'creating' 	=> JaminanCreating::class,
        'updated' 	=> JaminanUpdated::class,
        'updating' 	=> JaminanUpdating::class,
        'deleted' 	=> JaminanDeleted::class,
        'deleting' 	=> JaminanDeleting::class,
        'restoring' => JaminanRestoring::class,
        'restored' 	=> JaminanRestored::class,
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

	public function status_terakhir()
	{
		return $this->hasOne(MutasiJaminan::class, 'nomor_jaminan', 'nomor_jaminan')->orderby('tanggal', 'desc')->orderby('created_at', 'desc');
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
	public function setDokumenAttribute($variable)
	{
		$this->attributes['dokumen']	= json_encode($variable);
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

	public function getDokumenAttribute($variable)
	{
		return json_decode($this->attributes['dokumen'], true);
	}

	public function getPossibleActionAttribute($value){
		return null;
	}
}
