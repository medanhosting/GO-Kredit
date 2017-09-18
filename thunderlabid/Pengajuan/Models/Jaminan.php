<?php

namespace Thunderlabid\Pengajuan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Validator;

///////////////
// Exception //
///////////////
use Thunderlabid\Pengajuan\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Pengajuan\Events\Jaminan\JaminanCreating;
use Thunderlabid\Pengajuan\Events\Jaminan\JaminanCreated;
use Thunderlabid\Pengajuan\Events\Jaminan\JaminanUpdating;
use Thunderlabid\Pengajuan\Events\Jaminan\JaminanUpdated;
// use Thunderlabid\Pengajuan\Events\Jaminan\JaminanDeleting;
// use Thunderlabid\Pengajuan\Events\Jaminan\JaminanDeleted;

use Thunderlabid\Pengajuan\Traits\IDRTrait;

class Jaminan extends Model
{
	use SoftDeletes;
	use IDRTrait;

	protected $table	= 'p_jaminan';
	protected $fillable	= ['jenis', 'nilai_jaminan', 'tahun_perolehan', 'dokumen_jaminan', 'pengajuan_id'];
	protected $hidden	= [];
	protected $dates	= [];

	protected $rules		= [];
	protected $errors;
	protected $latest_analysis;

	public static $types	= ['bpkb', 'shm', 'shgb'];

	protected $events 		= [
		'creating' 	=> JaminanCreating::class,
		'created' 	=> JaminanCreated::class,
		'updating' 	=> JaminanUpdating::class,
		'updated' 	=> JaminanUpdated::class,
		// 'deleted' 	=> JaminanDeleted::class,
		// 'deleting' 	=> JaminanDeleting::class,
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
	public function setNilaiJaminanAttribute($variable)
	{
		$this->attributes['nilai_jaminan']		= $this->formatMoneyFrom($variable);
	}

	public function setDokumenJaminanAttribute($variable)
	{
		$this->attributes['dokumen_jaminan']	= json_encode($variable);
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
		$rules['jenis']					= ['required', 'in:' . implode(',',SELF::$types)];
		$rules['nilai_jaminan']			= ['required', 'numeric'];
		$rules['tahun_perolehan']		= ['required', 'date_format:"Y"', 'before:'.date('Y')];
		
		$rules['dokumen_jaminan.bpkb.jenis']			= ['required_if:jenis,bpkb', 'max:255', 'in:roda_2,roda_3,roda_4,roda_6'];
		$rules['dokumen_jaminan.bpkb.merk']				= ['required_if:jenis,bpkb', 'max:255'];
		$rules['dokumen_jaminan.bpkb.tahun']			= ['required_if:jenis,bpkb', 'date_format:"Y"', 'before:'.date('Y')];
		$rules['dokumen_jaminan.bpkb.nomor_bpkb']		= ['required_if:jenis,bpkb', 'max:255'];
		$rules['dokumen_jaminan.bpkb.atas_nama']		= ['required_if:jenis,bpkb', 'max:255'];
		$rules['dokumen_jaminan.bpkb.tipe']				= ['required_if:jenis,bpkb', 'max:255'];

		$rules['dokumen_jaminan.shm.tipe']				= ['required_if:jenis,shm', 'max:255', 'in:tanah,tanah_dan_bangunan'];
		$rules['dokumen_jaminan.shm.nomor_sertifikat']	= ['required_if:jenis,shm', 'max:255'];
		$rules['dokumen_jaminan.shm.atas_nama']			= ['required_if:jenis,shm', 'max:255'];
		$rules['dokumen_jaminan.shm.luas_tanah']		= ['required_if:jenis,shm', 'numeric'];
		$rules['dokumen_jaminan.shm.luas_bangunan']		= ['numeric', 'required_if:dokumen_jaminan.shm.tipe,tanah_dan_bangunan'];
		$rules['dokumen_jaminan.shm.alamat']			= ['required_if:jenis,shm', 'array'];

		$rules['dokumen_jaminan.shgb.tipe']						= ['required_if:jenis,shgb', 'max:255', 'in:tanah_dan_bangunan'];
		$rules['dokumen_jaminan.shgb.nomor_sertifikat']			= ['required_if:jenis,shgb', 'max:255'];
		$rules['dokumen_jaminan.shgb.masa_berlaku_sertifikat']	= ['required_if:jenis,shgb', 'max:255', 'date_format:"Y"'];
		$rules['dokumen_jaminan.shgb.atas_nama']				= ['required_if:jenis,shgb', 'max:255'];
		$rules['dokumen_jaminan.shgb.luas_tanah']				= ['required_if:jenis,shgb', 'numeric'];
		$rules['dokumen_jaminan.shgb.luas_bangunan']			= ['numeric', 'required_if:dokumen_jaminan.shgb.tipe,tanah_dan_bangunan'];
		$rules['dokumen_jaminan.shgb.alamat']					= ['required_if:jenis,shgb', 'array'];

		$rules['pengajuan_id']		= ['required'];

		$data 						= $this->attributes;
		$data['dokumen_jaminan'] 	= json_decode($data['dokumen_jaminan'], true);
		//////////////
		// Validate //
		//////////////
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

	public function getDokumenJaminanAttribute()
	{
		return json_decode($this->attributes['dokumen_jaminan'], true);
	}

	public function getNilaiJaminanAttribute()
	{
		return $this->formatMoneyTo($this->attributes['nilai_jaminan'], true);
	}

	public function getErrorsAttribute()
	{
		return $this->errors;
	}
}
