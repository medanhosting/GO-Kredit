<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

use Validator;
use App\Service\Traits\WaktuTrait;
use App\Service\Traits\IDRTrait;

////////////
// EVENTS //
////////////
use Thunderlabid\Kredit\Events\BuktiRealisasi\BuktiRealisasiCreated;
use Thunderlabid\Kredit\Events\BuktiRealisasi\BuktiRealisasiCreating;
use Thunderlabid\Kredit\Events\BuktiRealisasi\BuktiRealisasiUpdated;
use Thunderlabid\Kredit\Events\BuktiRealisasi\BuktiRealisasiUpdating;
use Thunderlabid\Kredit\Events\BuktiRealisasi\BuktiRealisasiDeleted;
use Thunderlabid\Kredit\Events\BuktiRealisasi\BuktiRealisasiDeleting;
use Thunderlabid\Kredit\Events\BuktiRealisasi\BuktiRealisasiRestored;
use Thunderlabid\Kredit\Events\BuktiRealisasi\BuktiRealisasiRestoring;

use Thunderlabid\Kredit\Models\Traits\FakturTrait;

class BuktiRealisasi extends Model
{
	use IDRTrait;
	use WaktuTrait;
	use FakturTrait;
	
	protected $table 	= 'k_bukti_realisasi';
	protected $fillable = ['nomor_pengajuan', 'nomor_transaksi', 'tanggal', 'jumlah', 'nip_karyawan'];
	protected $hidden 	= [];
	protected $appends	= ['jatuh_tempo'];

	protected $rules	= [];
	protected $errors;

	protected $events = [
		'created' 	=> BuktiRealisasiCreated::class,
		'creating' 	=> BuktiRealisasiCreating::class,
		'updated' 	=> BuktiRealisasiUpdated::class,
		'updating' 	=> BuktiRealisasiUpdating::class,
		'deleted' 	=> BuktiRealisasiDeleted::class,
		'deleting' 	=> BuktiRealisasiDeleting::class,
		'restoring' => BuktiRealisasiRestoring::class,
		'restored' 	=> BuktiRealisasiRestored::class,
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

	public function setJumlahAttribute($variable)
	{
		$this->attributes['jumlah']		= $this->formatMoneyFrom($variable);
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
		$rules['nomor_pengajuan']	= ['required', 'string'];
		$rules['nomor_transaksi'] 	= ['required', 'string'];
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

	public function getJumlahAttribute($variable)
	{
		return $this->formatmoneyTo($this->attributes['jumlah']);
	}
}
