<?php

namespace Thunderlabid\Kredit\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

use Validator, Config;
use Carbon\Carbon;
use App\Service\Traits\IDRTrait;
use App\Service\Traits\WaktuTrait;

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
	use WaktuTrait;

	protected $table 	= 'k_aktif';
	protected $fillable = ['nomor_kredit', 'nomor_pengajuan', 'jenis_pinjaman', 'nasabah', 'plafon_pinjaman', 'suku_bunga', 'jangka_waktu', 'provisi', 'administrasi', 'legal', 'kode_kantor', 'tanggal', 'ao', 'biaya_notaris'];
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
	public function angsuran(){
		return $this->hasMany(JadwalAngsuran::class, 'nomor_kredit', 'nomor_kredit');
	}

	public function tunggakan(){
		return $this->hasMany(JadwalAngsuran::class, 'nomor_kredit', 'nomor_kredit')->wherein('tag', ['pokok', 'bunga']);
	}

	public function denda(){
		return $this->hasMany(JadwalAngsuran::class, 'nomor_kredit', 'nomor_kredit')->wherein('tag', ['denda', 'restitusi_denda']);
	}

	public function titipan(){
		return $this->hasMany(JadwalAngsuran::class, 'nomor_kredit', 'nomor_kredit')->wherein('tag', ['titipan', 'pengambilan_titipan']);
	}

	public function angsuran_terakhir(){
		return $this->hasOne(JadwalAngsuran::class, 'nomor_kredit', 'nomor_kredit')->where('tag', 'pokok')->orderby('tanggal', 'desc');
	}

	public function penagihan(){
		return $this->hasMany(Penagihan::class, 'nomor_kredit', 'nomor_kredit');
	}

	public function jaminan(){
		return $this->hasMany(MutasiJaminan::class, 'nomor_kredit', 'nomor_kredit')->where('tag', 'in')->orderby('tanggal', 'desc');
	}
	
	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------
	public function scopePembayaranBerikut($query){
		return $query->selectraw('k_aktif.*')->selectraw(\DB::raw("(select min(tanggal) from k_jadwal_angsuran where k_jadwal_angsuran.nomor_kredit = k_aktif.nomor_kredit and k_jadwal_angsuran.deleted_at is null and k_jadwal_angsuran.nomor_faktur is null) as tanggal_pembayaran_berikut"))->selectraw(\DB::raw("(select sum(jumlah) from k_jadwal_angsuran where k_jadwal_angsuran.nomor_kredit = k_aktif.nomor_kredit and k_jadwal_angsuran.deleted_at is null and k_jadwal_angsuran.nomor_faktur is null and k_jadwal_angsuran.tanggal <= tanggal_pembayaran_berikut) as jumlah_pembayaran_berikut"));
	}

	public function scopeLihatJatuhTempo($query, Carbon $value){
		return $query->selectraw(\DB::raw("(select issued_at from k_angsuran where k_angsuran.nomor_kredit = k_aktif.nomor_kredit and k_angsuran.deleted_at is null and k_angsuran.paid_at is null and k_angsuran.issued_at <= '".$value->format('Y-m-d H:i:s')."' order by k_angsuran.issued_at asc limit 1 ) as issued_at"))
		->wherehas('angsuran', function($q)use($value){$q->wherenull('paid_at')->where('issued_at', '<=', $value->format('Y-m-d H:i:s'));});
	}

	public function scopeCekTunggakan($query, Carbon $value){
		return $query->select('k_aktif.*')
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td join k_angsuran where k_angsuran.id = td.angsuran_id and k_angsuran.nomor_kredit = k_aktif.nomor_kredit and td.deleted_at is null and k_angsuran.deleted_at is null and (k_angsuran.paid_at is null or datediff(k_angsuran.paid_at, k_angsuran.issued_at) > ".Config::get('kredit.batas_pembayaran_angsuran_hari').") and k_angsuran.issued_at <= '".$value->format('Y-m-d H:i:s')."') as tunggakan"))
			->selectraw(\DB::raw("(select issued_at from k_angsuran where k_angsuran.nomor_kredit = k_aktif.nomor_kredit and k_angsuran.deleted_at is null and (k_angsuran.paid_at is null or datediff(k_angsuran.paid_at, k_angsuran.issued_at) > ".Config::get('kredit.batas_pembayaran_angsuran_hari').") and k_angsuran.issued_at <= '".$value->format('Y-m-d H:i:s')."' order by k_angsuran.issued_at asc limit 1 ) as issued_at"))
			->wherehas('angsuran', function($q)use($value){$q->where(function($q){$q->wherenull('paid_at')->orwhereraw(\DB::raw('datediff(paid_at, issued_at) > '.Config::get('kredit.batas_pembayaran_angsuran_hari')));})->where('issued_at', '<=', $value->format('Y-m-d H:i:s'));})
		;
	}

	public function scopeKantor($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn('kode_kantor', $variable);
		}

		return $query->where('kode_kantor', $variable);
	}

	public function scopeBuatJurnalPagi($query, $sday, $eday){
		return $query->wherehas('angsuran', function($q)use($sday){$q->where(function($q)use($sday){
				$q
				->where('tanggal', '>=', $sday)
				->orwherenull('nota_bayar_id')
				;
			});})->with(['angsuran_terakhir', 'angsuran' => function($q)use($sday){$q
			->selectraw(\DB::raw('SUM(IF(tag="pokok",amount,0)) as pokok'))
			->selectraw(\DB::raw('SUM(IF(tag="bunga",amount,0)) as bunga'))
			->selectraw(\DB::raw('nth'))
			->selectraw(\DB::raw('nomor_kredit'))
			->wherein('tag', ['pokok', 'bunga'])
			->where('tanggal', '>=', $sday)
			->groupby('nth')
			->orderby('nth', 'asc')
			->groupby('nomor_kredit')
			;},
			'tunggakan' 	=> function($q)use($sday){$q
			->selectraw(\DB::raw('SUM(IF(tag="pokok",amount,0)) as pokok'))
			->selectraw(\DB::raw('SUM(IF(tag="bunga",amount,0)) as bunga'))
			->selectraw(\DB::raw('min(nth) as nth'))
			->selectraw(\DB::raw('count(nth) as tgk'))
			->selectraw(\DB::raw('nomor_kredit'))
			->TunggakanBeberapaWaktuLalu(Carbon::parse($sday))
			->groupby('nomor_kredit')
			;},
			'denda' 	=> function($q)use($sday){$q
			->selectraw(\DB::raw('SUM(IF(tag="denda",amount,IF(tag="restitusi_denda",amount,0))) as denda'))
			->selectraw(\DB::raw('nomor_kredit'))
			->where('tanggal', '<', $sday)
			->groupby('nomor_kredit')
			;},
			'titipan' 	=> function($q)use($sday){$q
			->selectraw(\DB::raw('SUM(IF(tag="titipan",amount,IF(tag="pengambilan_titipan",amount,0))) as titipan'))
			->selectraw(\DB::raw('nomor_kredit'))
			->where('tanggal', '<', $sday)
			->groupby('nomor_kredit')
			;},
			]);
	}

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

	public function setBiayaNotarisAttribute($variable)
	{
		$this->attributes['biaya_notaris']		= $this->formatMoneyFrom($variable);
	}

	public function setLegalAttribute($variable)
	{
		$this->attributes['legal']				= $this->formatMoneyFrom($variable);
	}

	public function setTanggalAttribute($variable)
	{
		$this->attributes['tanggal']			= $this->formatDateTimeFrom($variable);
	}

	public function setNasabahAttribute($variable)
	{
		$this->attributes['nasabah']			= json_encode($variable);
	}

	public function setAoAttribute($variable)
	{
		$this->attributes['ao']					= json_encode($variable);
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

	public function getBiayaNotarisAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['biaya_notaris']);
	}

	public function getLegalAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['legal']);
	}

	public function getTanggalAttribute($variable)
	{
		return $this->formatDateTimeTo($this->attributes['tanggal']);
	}

	public function getNasabahAttribute($variable)
	{
		return json_decode($this->attributes['nasabah'], true);
	}

	public function getAoAttribute($variable)
	{
		return json_decode($this->attributes['ao'], true);
	}
}
