<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

use Validator, Carbon\Carbon;
use App\Service\Traits\WaktuTrait;
use App\Service\Traits\IDRTrait;

////////////
// EVENTS //
////////////
use Thunderlabid\Kredit\Events\SuratPeringatan\SuratPeringatanCreated;
use Thunderlabid\Kredit\Events\SuratPeringatan\SuratPeringatanCreating;
use Thunderlabid\Kredit\Events\SuratPeringatan\SuratPeringatanUpdated;
use Thunderlabid\Kredit\Events\SuratPeringatan\SuratPeringatanUpdating;
use Thunderlabid\Kredit\Events\SuratPeringatan\SuratPeringatanDeleted;
use Thunderlabid\Kredit\Events\SuratPeringatan\SuratPeringatanDeleting;
use Thunderlabid\Kredit\Events\SuratPeringatan\SuratPeringatanRestored;
use Thunderlabid\Kredit\Events\SuratPeringatan\SuratPeringatanRestoring;

use Thunderlabid\Kredit\Models\Traits\FakturTrait;

class SuratPeringatan extends Model
{
	use IDRTrait;
	use WaktuTrait;
	use FakturTrait;
	
	protected $table 	= 'k_surat_peringatan';
	protected $fillable = ['nomor_kredit', 'nth', 'tanggal', 'tag', 'karyawan', 'penagihan_id'];
	protected $hidden 	= [];
	protected $appends	= ['nomor_surat'];

	protected $rules	= [];
	protected $errors;

	protected $events = [
		'created' 	=> SuratPeringatanCreated::class,
		'creating' 	=> SuratPeringatanCreating::class,
		'updated' 	=> SuratPeringatanUpdated::class,
		'updating' 	=> SuratPeringatanUpdating::class,
		'deleted' 	=> SuratPeringatanDeleted::class,
		'deleting' 	=> SuratPeringatanDeleting::class,
		'restoring' => SuratPeringatanRestoring::class,
		'restored' 	=> SuratPeringatanRestored::class,
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

	public function penagihan(){
		return $this->hasone(Penagihan::class, 'surat_peringatan_id')->orderby('created_at', 'desc');
	}
	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------
	public function scopeNomorSurat($q, $v){

		$tag['SP.001'] 	= 'surat_peringatan_1';
		$tag['SP.002'] 	= 'surat_peringatan_2';
		$tag['SP.003'] 	= 'surat_peringatan_3';
		$tag['SP.004'] 	= 'surat_somasi_1';
		$tag['SP.005'] 	= 'surat_somasi_2';
		$tag['SP.006'] 	= 'surat_somasi_3';

		list($nk, $tg)	= explode('-', $v);
		return $q->where('nomor_kredit', $nk)->where('tag', $tag[$tg]);
	}

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setTanggalAttribute($variable)
	{
		$this->attributes['tanggal']	= $this->formatDateTimeFrom($variable);
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
		$rules['nomor_kredit']		= ['required', 'string'];
		$rules['nth'] 				= ['required', 'integer'];
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

	public function getKaryawanAttribute($variable)
	{
		return json_decode($this->attributes['karyawan'], true);
	}

	public function getNomorSuratAttribute($variable)
	{
		$tag['surat_peringatan_1'] 	= 'SP.001';
		$tag['surat_peringatan_2'] 	= 'SP.002';
		$tag['surat_peringatan_3'] 	= 'SP.003';
		$tag['surat_somasi_1'] 		= 'SP.004';
		$tag['surat_somasi_2'] 		= 'SP.005';
		$tag['surat_somasi_3'] 		= 'SP.006';

		return $this->nomor_kredit.'-'.$tag[$this->attributes['tag']];
	}
}
