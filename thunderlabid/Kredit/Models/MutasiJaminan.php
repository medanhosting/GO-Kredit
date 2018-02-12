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
	protected $fillable = ['nomor_jaminan', 'tanggal', 'status', 'tag', 'progress', 'deskripsi', 'karyawan'];
	protected $hidden 	= [];
	protected $appends	= ['possible_action'];
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
	public function jaminan(){
		return $this->belongsto(Jaminan::class, 'nomor_jaminan', 'nomor_jaminan');
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
		$rules['nomor_jaminan']		= ['required', 'string'];
		$rules['tanggal'] 			= ['required', 'date_format:"Y-m-d H:i:s"'];
		$rules['status'] 			= ['required', 'string'];

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

	public function getPossibleActionAttribute($value){
		// Kalau Jaminan (Aktif/Titipan/HapusBuku) di Dalam
		// 	=> Bermasalah di Dalam
		// 	=> Jaminan Keluar & (Aktif/Titipan/HapusBuku)
		// Kalau Jaminan (Aktif/Titipan/HapusBuku) di Luar
		// 	=> Bermasalah di Luar
		// 	=> Jaminan Masuk & (Aktif/Titipan/HapusBuku)
		// Kalau Jaminan Bermasalah di Luar
		// 	=> Bermasalah di Dalam
		// 	=> Jaminan Luar & Hapus Buku
		// Kalau Jaminan Bermasalah di Dalam
		// 	=> Bermasalah di Luar
		// 	=> Jaminan Dalam & Hapus Buku

		switch ($this->tag) {
			case 'in':
				if(in_array($this->status, ['aktif'])){
					$next['Tandai Jaminan Bermasalah']	= 'bermasalah-in';
					$next['Tandai Jaminan Keluar']		= $this->status.'-out';
				}elseif(str_is($this->status, 'bermasalah')){
					$next['Hapus Buku']	 				= 'hapus_buku-out';
					$next['Tandai Jaminan Keluar']		= 'bermasalah-out';
				}elseif(str_is($this->status, 'titipan')){
					$next['Hapus Buku']	 				= 'hapus_buku-out';
				}
				break;
			case 'out':
				if(in_array($this->status, ['aktif'])){
					$next['Tandai Jaminan Bermasalah']	= 'bermasalah-out';
					$next['Tandai Jaminan Masuk']		= $this->status.'-in';
				}elseif(str_is($this->status, 'bermasalah')){
					$next['Hapus Buku']	 				= 'hapus_buku-out';
					$next['Tandai Jaminan Bermasalah']	= 'bermasalah-in';
				}elseif(str_is($this->status, 'titipan')){
					$next['Hapus Buku']	 				= 'hapus_buku-out';
				}

				break;
		}
		if(str_is($this->progress, 'menunggu_validasi')){
			$next[] 	= 'validasi';
		}

		return $next;
	}
}
