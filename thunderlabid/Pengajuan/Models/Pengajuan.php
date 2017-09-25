<?php

namespace Thunderlabid\Pengajuan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Validator, DB;

///////////////
// Exception //
///////////////
use Thunderlabid\Pengajuan\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanCreating;
use Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanCreated;
use Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanUpdating;
use Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanUpdated;
use Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanDeleting;
use Thunderlabid\Pengajuan\Events\Pengajuan\PengajuanDeleted;

////////////
// TRAITS //
////////////
use Thunderlabid\Pengajuan\Traits\IDRTrait;
use Thunderlabid\Pengajuan\Traits\TanggalTrait;
use Thunderlabid\Pengajuan\Traits\WaktuTrait;
use Thunderlabid\Pengajuan\Traits\NIKTrait;

class Pengajuan extends Model
{
	use SoftDeletes;

	use NIKTrait;
	use IDRTrait;
	use TanggalTrait;
	use WaktuTrait;

	protected $table	= 'p_pengajuan';
	protected $fillable	= ['pokok_pinjaman', 'kemampuan_angsur', 'is_mobile', 'nasabah', 'dokumen_pelengkap', 'kode_kantor', 'ao'];
	protected $hidden	= [];
	protected $dates	= [];

	protected $rules	= [];
	protected $errors;
	protected $latest_analysis;
	protected $appends 	= ['tanggal'];

	protected $keyType  	= 'string';
    public $incrementing 	= false;
    
	protected $events 	= [
		'creating' 	=> PengajuanCreating::class,
		'created' 	=> PengajuanCreated::class,
		'updating' 	=> PengajuanUpdating::class,
		'updated' 	=> PengajuanUpdated::class,
		'deleting' 	=> PengajuanDeleting::class,
		'deleted' 	=> PengajuanDeleted::class,
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
	public function status_terakhir()
	{
		return $this->hasOne(Status::class, 'pengajuan_id')->orderby('tanggal', 'desc');
	}

	public function status_permohonan()
	{
		return $this->hasOne(Status::class, 'pengajuan_id')->where('status', 'permohonan')->orderby('tanggal', 'asc');
	}

	public function jaminan()
	{
		return $this->hasMany(Jaminan::class, 'pengajuan_id');
	}

	public function jaminan_kendaraan()
	{
		return $this->hasMany(Jaminan::class, 'pengajuan_id')->where('jenis', 'bpkb');
	}

	public function jaminan_tanah_bangunan()
	{
		return $this->hasMany(Jaminan::class, 'pengajuan_id')->whereIn('jenis', ['shm', 'shgb'])->orderby('jenis', 'asc');
	}

	public function riwayat_status()
	{
		return $this->hasMany(Status::class, 'pengajuan_id');
	}
	
	public function setAoAttribute($variable)
	{
		$this->attributes['ao']		= json_encode($variable);
	}

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------
	public function scopeKantor($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn('kode_kantor', $variable);
		}

		return $query->where('kode_kantor', $variable);
	}

	public function scopeStatus($query, $variable)
	{
		$query 	= $query->select('p_pengajuan.*');

		return $query->whereHas('status_terakhir', function($q)use($variable){$q->status($variable);});
		// if(!is_array($variable))
		// {
			return $query
			 ->join('p_status', function ($join) use($variable) 
			 {
									$join->on ( 'p_status.pengajuan_id', '=', 'p_pengajuan.id' )
									// ->on(DB::raw('(p_status.id = (select id from p_status as tl2 where tl2.pengajuan_id = p_status.pengajuan_id desc limit 1))'), DB::raw(''), DB::raw(''))
									// ->where('p_status.status', '=', $variable)
									// ->wherenull('p_status.deleted_at')
									;
			})
			;
		// }
		// else
		// {
		// 	return $query
		// 	 ->join('p_status', function ($join) use($variable) 
		// 	 {
		// 							$join->on ( 'p_status.pengajuan_id', '=', 'p_pengajuan.id' )
		// 							->on(DB::raw('(p_status.id = (select id from p_status as tl2 where tl2.pengajuan_id = p_status.pengajuan_id and tl2.deleted_at is null order by tl2.tanggal desc limit 1))'), DB::raw(''), DB::raw(''))
		// 							->whereIn('p_status.status', $variable)
		// 							->wherenull('p_status.deleted_at')
		// 							;
		// 	})
		// 	;
		// }



		if(is_array($variable))
		{
			$to_string 				= [];
			foreach ($variable as $key => $value) 
			{
				$to_string[$key] 	= '"'.$value.'"';
			}
			$status 				= implode(',', $to_string);

			return $query->selectraw('p_pengajuan.*')->whereraw(DB::raw('(IFNULL((select p_status_now.status from p_status as p_status_now join p_status on p_status.id = p_status_now.id order by p_status_now.tanggal desc limit 1), "permohonan")) in ('.$status.')'));
		}

//(p_status.id = (select id from p_status as tl2 where tl2.pengajuan_id = p_status.pengajuan_id and tl2.deleted_at is null order by tl2.tanggal desc limit 1))
		$query= $query->selectraw('p_pengajuan.*')->selectraw(DB::raw('(IFNULL((select p_status.status from p_status join p_pengajuan on p_pengajuan.id = p_status.pengajuan_id and p_status.id = (IFNULL((select p_status_now.id from p_status as p_status_now join p_status on p_status.id = p_status_now.id order by p_status_now.tanggal desc limit 1), "123")) limit 1), "permohonan")) '));
		dD($query->skip(30)->first());
	}

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setKemampuanAngsurAttribute($variable)
	{
		$this->attributes['kemampuan_angsur']	= $this->formatMoneyFrom($variable);
	}

	public function setPokokPinjamanAttribute($variable)
	{
		$this->attributes['pokok_pinjaman']	= $this->formatMoneyFrom($variable);
	}

	public function setNasabahAttribute($variable)
	{
		//NIK
		if(isset($variable['nik']))
		{
			$variable['nik']			= $this->formatNikFrom($variable['nik']);
		}

		//TANGGAL LAHIR
		if(isset($variable['tanggal_lahir']))
		{
			$variable['tanggal_lahir']	= $this->formatDateFrom($variable['tanggal_lahir']);
		}

		//PENGHASILAN BERSIH
		if(isset($variable['penghasilan_bersih']))
		{
			$variable['penghasilan_bersih']	= $this->formatMoneyFrom($variable['penghasilan_bersih']);
		}

		$this->attributes['nasabah']		= json_encode($variable);
	}

	public function setDokumenPelengkapAttribute($variable)
	{
		$this->attributes['dokumen_pelengkap']	= json_encode($variable);
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
		$rules['pokok_pinjaman']				= ['required', 'numeric'];
		$rules['kemampuan_angsur']				= ['required', 'numeric'];
		$rules['is_mobile']						= ['boolean'];
		$rules['dokumen_pelengkap.ktp']			= ['required', 'url'];
		$rules['dokumen_pelengkap.kk']			= ['required_with:nasabah.keluarga', 'url'];
		$rules['kode_kantor']					= ['required_if:is_mobile,false'];

		$rules['nasabah.nik']					= ['required_if:is_mobile,false', 'max:255'];
		$rules['nasabah.nama']					= ['required_if:is_mobile,false', 'max:255'];
		$rules['nasabah.tanggal_lahir']			= ['required_if:is_mobile,false', 'date_format:"Y-m-d"'];
		$rules['nasabah.tempat_lahir']			= ['required_if:is_mobile,false', 'max:255'];
		$rules['nasabah.jenis_kelamin']			= ['required_if:is_mobile,false', 'in:laki-laki,perempuan'];
		$rules['nasabah.status_perkawinan']		= ['required_if:is_mobile,false', 'in:belum_kawin,kawin,cerai,cerai_mati'];
		$rules['nasabah.pekerjaan']				= ['required_if:is_mobile,false', 'max:255'];
		$rules['nasabah.penghasilan_bersih']	= ['required_if:is_mobile,false', 'numeric'];
		$rules['nasabah.telepon']				= ['required', 'max:40'];
		$rules['nasabah.nomor_whatsapp']		= ['max:255'];
		$rules['nasabah.email']					= ['max:40'];
		$rules['nasabah.alamat']				= ['required_if:is_mobile,false', 'array'];
		$rules['nasabah.keluarga']				= ['array'];

		$data 						= $this->attributes;
		$data['nasabah'] 			= json_decode($data['nasabah'], true);
		$data['dokumen_pelengkap'] 	= json_decode($data['dokumen_pelengkap'], true);

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

	public function getKemampuanAngsurAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['kemampuan_angsur']);
	}

	public function getPokokPinjamanAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['pokok_pinjaman']);
	}

	public function getTanggalAttribute($variable)
	{
		return $this->formatDateTimeTo($this->attributes['created_at']);
	}

	public function getNasabahAttribute($variable)
	{
		$variable 							= json_decode($this->attributes['nasabah'], true);

		//NIK
		if(isset($variable['nik']))
		{
			$variable['nik']				= $this->formatNikTo($variable['nik']);
		}

		//TANGGAL LAHIR
		if(isset($variable['tanggal_lahir']))
		{
			$variable['tanggal_lahir']		= $this->formatDateTo($variable['tanggal_lahir']);
		}

		//PENGHASILAN BERSIH
		if(isset($variable['penghasilan_bersih']))
		{
			$variable['penghasilan_bersih']	= $this->formatMoneyTo($variable['penghasilan_bersih']);
		}

		return $variable;
	}

	public function getAoAttribute($variable)
	{
		return json_decode($this->attributes['ao'], true);
	}

	public function getDokumenPelengkapAttribute($variable)
	{
		return json_decode($this->attributes['dokumen_pelengkap'], true);
	}

	public function getErrorsAttribute()
	{
		return $this->errors;
	}
}
