<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

use Validator, Carbon\Carbon;

use App\Service\Traits\WaktuTrait;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Kredit\Events\Penagihan\PenagihanCreated;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanCreating;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanUpdated;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanUpdating;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanDeleted;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanDeleting;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanRestored;
use Thunderlabid\Kredit\Events\Penagihan\PenagihanRestoring;

class Penagihan extends Model
{
	use WaktuTrait;

	protected $table 	= 'k_penagihan';
	protected $fillable = ['nomor_kredit', 'nip_karyawan', 'tanggal'];
	protected $hidden 	= [];
	protected $appends	= [];
	protected $rules	= [];
	protected $errors;

	protected $events = [
        'created' 	=> PenagihanCreated::class,
        'creating' 	=> PenagihanCreating::class,
        'updated' 	=> PenagihanUpdated::class,
        'updating' 	=> PenagihanUpdating::class,
        'deleted' 	=> PenagihanDeleted::class,
        'deleting' 	=> PenagihanDeleting::class,
        'restoring' => PenagihanRestoring::class,
        'restored' 	=> PenagihanRestored::class,
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
	public function scopeHitungTunggakan($query, Carbon $start, Carbon $end){
		return $query->select('k_penagihan.*')
			// ->selectraw('(select sum(ad.amount) from k_angsuran_detail as ad where (ad.tanggal <= k_penagihan.tanggal or (ad.tag in ("denda", "collector") and ad.tanggal <= "'.$value->format('Y-m-d H:i:s').'")) and ad.nomor_kredit = k_penagihan.nomor_kredit) as tunggakan')
			->selectraw('(select sum(ad.amount) from k_angsuran_detail as ad where ad.tanggal <= k_penagihan.tanggal and ad.nomor_kredit = k_penagihan.nomor_kredit) as tunggakan')
			->selectraw('(select min(ad.tanggal) from k_angsuran_detail as ad where ad.tanggal <= k_penagihan.tanggal and ad.nomor_kredit = k_penagihan.nomor_kredit) as tanggal_jatuh_tempo')
			;
	}

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setTanggalAttribute($variable)
	{
		$this->attributes['tanggal']	= $this->formatDateTimeFrom($variable);
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
		$rules['nip_karyawan'] 		= ['required', 'string'];
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

}
