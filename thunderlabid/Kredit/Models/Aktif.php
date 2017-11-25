<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

use Validator;

use App\Service\Traits\IDRTrait;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Kredit\Events\Aktif\AktifCreated;
use Thunderlabid\Kredit\Events\Aktif\AktifCreating;
use Thunderlabid\Kredit\Events\Aktif\AktifUpdated;
use Thunderlabid\Kredit\Events\Aktif\AktifUpdating;
use Thunderlabid\Kredit\Events\Aktif\AktifDeleted;
use Thunderlabid\Kredit\Events\Aktif\AktifDeleting;
use Thunderlabid\Kredit\Events\Aktif\AktifRestored;
use Thunderlabid\Kredit\Events\Aktif\AktifRestoring;

class Aktif extends Model
{
	use IDRTrait;

	protected $table 	= 'k_aktif';
	protected $fillable = ['nomor_kredit', 'nomor_pengajuan', 'jenis_pinjaman', 'nasabah', 'plafon_pinjaman', 'suku_bunga', 'jangka_waktu', 'provisi', 'administrasi', 'legal', 'kode_kantor'];
	protected $hidden 	= [];
	protected $appends	= [];

	protected $rules	= [];
	protected $errors;

	protected $events = [
        'created' 	=> AktifCreated::class,
        'creating' 	=> AktifCreating::class,
        'updated' 	=> AktifUpdated::class,
        'updating' 	=> AktifUpdating::class,
        'deleted' 	=> AktifDeleted::class,
        'deleting' 	=> AktifDeleting::class,
        'restoring' => AktifRestoring::class,
        'restored' 	=> AktifRestored::class,
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
	public function setPlafonPinjamanAttribute($variable)
	{
		$this->attributes['plafon_pinjaman']	= $this->formatMoneyFrom($variable);
	}

	public function setAdministrasiAttribute($variable)
	{
		$this->attributes['administrasi']		= $this->formatMoneyFrom($variable);
	}

	public function setLegalAttribute($variable)
	{
		$this->attributes['legal']				= $this->formatMoneyFrom($variable);
	}

	public function setNasabahAttribute($variable)
	{
		$this->attributes['nasabah']			= json_encode($variable);
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
		$rules['nomor_kredit'] 		= ['required', 'string'];
		$rules['nomor_pengajuan'] 	= ['required', 'string'];
		$rules['nasabah'] 			= ['required'];
		$rules['plafon_pinjaman']	= ['required', 'numeric'];
		$rules['suku_bunga']		= ['required', 'numeric'];
		$rules['jangka_waktu']		= ['required', 'numeric'];
		$rules['provisi']			= ['required', 'numeric'];
		$rules['administrasi']		= ['required', 'numeric'];
		$rules['legal']				= ['required', 'numeric'];

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

	public function getPlafonPinjamanAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['plafon_pinjaman']);
	}

	public function getAdministrasiAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['administrasi']);
	}

	public function getLegalAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['legal']);
	}

	public function getNasabahAttribute($variable)
	{
		return json_decode($this->attributes['nasabah'], true);
	}
}
