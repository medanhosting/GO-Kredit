<?php

namespace Thunderlabid\Survei\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Validator, DB;

///////////////
// Exception //
///////////////
use Thunderlabid\Survei\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailCreating;
use Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailCreated;
use Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailUpdating;
use Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailUpdated;
// use Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailDeleting;
// use Thunderlabid\Survei\Events\SurveiDetail\SurveiDetailDeleted;

use Thunderlabid\Survei\Traits\TanggalTrait;
use Thunderlabid\Survei\Traits\WaktuTrait;
use Thunderlabid\Survei\Traits\IDRTrait;

class SurveiDetail extends Model
{
	use SoftDeletes;
	use TanggalTrait;
	use WaktuTrait;
	use IDRTrait;

	protected $table	= 's_survei_detail';
	protected $fillable	= ['survei_id', 'jenis', 'dokumen_survei'];
	protected $hidden	= [];
	protected $dates	= [];

	protected $rules	= [];
	protected $errors;
	protected $latest_analysis;

	public static $types	= ['character', 'condition', 'capacity', 'capital', 'collateral'];
    
	protected $events 	= [
		'creating' 	=> SurveiDetailCreating::class,
		'created' 	=> SurveiDetailCreated::class,
		'updating' 	=> SurveiDetailUpdating::class,
		'updated' 	=> SurveiDetailUpdated::class,
		// 'deleted' 	=> SurveiDetailDeleted::class,
		// 'deleting' 	=> SurveiDetailDeleting::class,
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
	public function survei()
	{
		return $this->belongsTo(Survei::class, 'survei_id');
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
	
	public function setDokumenSurveiAttribute($variable)
	{
		if(str_is($this->jenis, 'capacity'))
		{
			$variable['capacity']['penghasilan']['gaji']		= $this->formatMoneyFrom($variable['capacity']['penghasilan']['gaji']);
			$variable['capacity']['penghasilan']['pasangan']	= $this->formatMoneyFrom($variable['capacity']['penghasilan']['pasangan']);
			$variable['capacity']['penghasilan']['usaha']		= $this->formatMoneyFrom($variable['capacity']['penghasilan']['usaha']);
			$variable['capacity']['penghasilan']['lain_lain']	= $this->formatMoneyFrom($variable['capacity']['penghasilan']['lain_lain']);
			$variable['capacity']['pengeluaran']['biaya_produksi']		= $this->formatMoneyFrom($variable['capacity']['pengeluaran']['biaya_produksi']);
			$variable['capacity']['pengeluaran']['biaya_rumah_tangga']	= $this->formatMoneyFrom($variable['capacity']['pengeluaran']['biaya_rumah_tangga']);
			$variable['capacity']['pengeluaran']['biaya_listrik_pdam_telepon']	= $this->formatMoneyFrom($variable['capacity']['pengeluaran']['biaya_listrik_pdam_telepon']);
			$variable['capacity']['pengeluaran']['biaya_pendidikan']	= $this->formatMoneyFrom($variable['capacity']['pengeluaran']['biaya_pendidikan']);
			$variable['capacity']['pengeluaran']['lain_lain']	= $this->formatMoneyFrom($variable['capacity']['pengeluaran']['lain_lain']);
			$variable['capacity']['kemampuan_angsur']			= $this->formatMoneyFrom($variable['capacity']['kemampuan_angsur']);
		}

		if(str_is($this->jenis, 'capital'))
		{
			if(isset($variable['capital']['rumah']['angsuran_bulanan']))
			{
				$variable['capital']['rumah']['angsuran_bulanan']	= $this->formatMoneyFrom($variable['capital']['rumah']['angsuran_bulanan']);
			}
			$variable['capital']['rumah']['nilai_rumah']			= $this->formatMoneyFrom($variable['capital']['rumah']['nilai_rumah']);
			$variable['capital']['kendaraan']['nilai_kendaraan']	= $this->formatMoneyFrom($variable['capital']['kendaraan']['nilai_kendaraan']);
			$variable['capital']['usaha']['nilai_aset']				= $this->formatMoneyFrom($variable['capital']['usaha']['nilai_aset']);
		}

		if(str_is($this->jenis, 'collateral') && str_is($variable['collateral']['jenis'], 'bpkb'))
		{
			$variable['collateral']['bpkb']['harga_taksasi']		= $this->formatMoneyFrom($variable['collateral']['bpkb']['harga_taksasi']);
			$variable['collateral']['bpkb']['harga_bank']			= $this->formatMoneyFrom($variable['collateral']['bpkb']['harga_bank']);
			$variable['collateral']['bpkb']['masa_berlaku_stnk']	= $this->formatDateFrom($variable['collateral']['bpkb']['masa_berlaku_stnk']);
		}

		if(str_is($this->jenis, 'collateral') && str_is($variable['collateral']['jenis'], 'shm'))
		{
			if(isset($variable['collateral']['shm']['njop_bangunan']))
			{
				$variable['collateral']['shm']['njop_bangunan']		= $this->formatMoneyFrom($variable['collateral']['shm']['njop_bangunan']);
			}
			if(isset($variable['collateral']['shm']['njop_bangunan']))
			{
				$variable['collateral']['shm']['nilai_bangunan']	= $this->formatMoneyFrom($variable['collateral']['shm']['nilai_bangunan']);
			}
			$variable['collateral']['shm']['njop_tanah']	= $this->formatMoneyFrom($variable['collateral']['shm']['njop_tanah']);
			$variable['collateral']['shm']['nilai_tanah']	= $this->formatMoneyFrom($variable['collateral']['shm']['nilai_tanah']);
			$variable['collateral']['shm']['harga_taksasi']	= $this->formatMoneyFrom($variable['collateral']['shm']['harga_taksasi']);
			
		}

		if(str_is($this->jenis, 'collateral') && str_is($variable['collateral']['jenis'], 'shgb'))
		{
			$variable['collateral']['shgb']['njop_bangunan']	= $this->formatMoneyFrom($variable['collateral']['shgb']['njop_bangunan']);
			$variable['collateral']['shgb']['nilai_bangunan']	= $this->formatMoneyFrom($variable['collateral']['shgb']['nilai_bangunan']);
			$variable['collateral']['shgb']['njop_tanah']		= $this->formatMoneyFrom($variable['collateral']['shgb']['njop_tanah']);
			$variable['collateral']['shgb']['nilai_tanah']		= $this->formatMoneyFrom($variable['collateral']['shgb']['nilai_tanah']);
			$variable['collateral']['shgb']['harga_taksasi']	= $this->formatMoneyFrom($variable['collateral']['shgb']['harga_taksasi']);			
		}

		$dokumen_survei 	= json_encode($variable);

		$this->attributes['dokumen_survei']	= $dokumen_survei;
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
		$rules['survei_id']									= ['required', 'exists:s_survei,id'];
		$rules['jenis']										= ['required', 'in'. implode(',',SELF::$types)];
		
		//CHARACTER
		$rules['dokumen_survei.character.lingkungan_tinggal']	= ['required_if:jenis,character', 'in:dikenal,kurang_dikenal,tidak_dikenal'];
		$rules['dokumen_survei.character.lingkungan_kerja']		= ['required_if:jenis,character', 'in:dikenal,kurang_dikenal,tidak_dikenal'];
		$rules['dokumen_survei.character.watak']				= ['required_if:jenis,character', 'in:baik,cukup_baik,tidak_baik'];
		$rules['dokumen_survei.character.pola_hidup']			= ['required_if:jenis,character', 'in:mewah,sederhana'];
		$rules['dokumen_survei.character.informasi.*']			= ['required_if:jenis,character'];
		
		//CONDITION
		$rules['dokumen_survei.condition.persaingan_usaha']			= ['required_if:jenis,condition', 'in:padat,sedang,biasa'];
		$rules['dokumen_survei.condition.prospek_usaha']			= ['required_if:jenis,condition', 'in:padat,sedang,biasa'];
		$rules['dokumen_survei.condition.perputaran_usaha']			= ['required_if:jenis,condition', 'in:padat,sedang,lambat'];
		$rules['dokumen_survei.condition.pengalaman_usaha']			= ['required_if:jenis,condition'];
		$rules['dokumen_survei.condition.resiko_usaha_kedepan']		= ['required_if:jenis,condition', 'in:bagus,biasa,suram'];
		$rules['dokumen_survei.condition.jumlah_pelanggan_harian']	= ['required_if:jenis,condition'];

		//CAPACITY
		$rules['dokumen_survei.capacity.manajemen_usaha']				= ['required_if:jenis,capacity', 'in:baik,cukup_baik,tidak_baik'];
		$rules['dokumen_survei.capacity.penghasilan.gaji']				= ['required_if:jenis,capacity', 'numeric'];
		$rules['dokumen_survei.capacity.penghasilan.pasangan']			= ['numeric'];
		$rules['dokumen_survei.capacity.penghasilan.usaha']				= ['numeric'];
		$rules['dokumen_survei.capacity.penghasilan.lain_lain']			= ['numeric'];
		$rules['dokumen_survei.capacity.pengeluaran.biaya_produksi']	= ['numeric'];
		$rules['dokumen_survei.capacity.pengeluaran.biaya_rumah_tangga']= ['numeric'];
		$rules['dokumen_survei.capacity.pengeluaran.biaya_listrik_pdam_telepon'] 		= ['numeric'];
		$rules['dokumen_survei.capacity.pengeluaran.biaya_pendidikan']	= ['numeric'];
		$rules['dokumen_survei.capacity.pengeluaran.lain_lain']			= ['numeric'];
		$rules['dokumen_survei.capacity.kemampuan_angsur']				= ['numeric'];
		$rules['dokumen_survei.capacity.sumber_pengembalian_pokok_pinjaman']= ['required_if:jenis,capacity'];
		$rules['dokumen_survei.capacity.tempat_kerja_pasangan']			= ['required_with:dokumen_survei.capacity.penghasilan.pasangan'];

		//CAPITAL
		$rules['dokumen_survei.capital.rumah.status']			= ['required_if:jenis,capital','in:milik_sendiri,keluarga,dinas,sewa'];
		$rules['dokumen_survei.capital.rumah.sewa_sejak']		= ['required_if:dokumen_survei.capital.rumah.status,sewa'];
		$rules['dokumen_survei.capital.rumah.masa_sewa']		= ['required_if:dokumen_survei.capital.rumah.status,sewa'];
		$rules['dokumen_survei.capital.rumah.angsuran_bulanan']	= ['numeric'];
		$rules['dokumen_survei.capital.rumah.lama_angsuran']	= ['required_with:dokumen_survei.capital.rumah.angsuran_bulanan', 'numeric'];
		$rules['dokumen_survei.capital.rumah.lama_menempati']	= ['required_if:jenis,capital', 'numeric'];
		$rules['dokumen_survei.capital.rumah.luas_rumah']		= ['required_if:jenis,capital', 'numeric'];
		$rules['dokumen_survei.capital.rumah.nilai_rumah']		= ['required_if:jenis,capital', 'numeric'];

		$rules['dokumen_survei.capital.kendaraan.jumlah_kendaraan_roda_4']	= ['required_if:jenis,capital', 'numeric'];
		$rules['dokumen_survei.capital.kendaraan.jumlah_kendaraan_roda_2']	= ['required_if:jenis,capital', 'numeric'];
		$rules['dokumen_survei.capital.kendaraan.nilai_kendaraan']			= ['required_if:jenis,capital', 'numeric'];
		
		$rules['dokumen_survei.capital.usaha.nama_usaha']		= ['required_if:jenis,capital', 'max:255'];
		$rules['dokumen_survei.capital.usaha.bidang_usaha']		= ['required_if:jenis,capital', 'max:255'];
		$rules['dokumen_survei.capital.usaha.lama_usaha']		= ['required_if:jenis,capital', 'numeric'];
		$rules['dokumen_survei.capital.usaha.status_usaha']		= ['required_if:jenis,capital', 'in:milik_sendiri,milik_keluarga,kerjasama_bagi_hasil'];
		$rules['dokumen_survei.capital.usaha.bagi_hasil']		= ['required_if:dokumen_survei.capital.usaha.status_usaha,kerjasama_bagi_hasil'];
		$rules['dokumen_survei.capital.usaha.nilai_aset']		= ['required_if:jenis,capital'];

		//COLLATERAL
		$rules['dokumen_survei.collateral.jenis']			= ['required_if:jenis,collateral', 'in:bpkb,shm,shgb'];

		$rules['dokumen_survei.collateral.bpkb.merk']			= ['required_if:dokumen_survei.collateral.jenis,bpkb'];
		$rules['dokumen_survei.collateral.bpkb.jenis']			= ['required_if:dokumen_survei.collateral.jenis,bpkb'];
		$rules['dokumen_survei.collateral.bpkb.nomor_polisi']	= ['required_if:dokumen_survei.collateral.jenis,bpkb'];
		$rules['dokumen_survei.collateral.bpkb.warna']			= ['required_if:dokumen_survei.collateral.jenis,bpkb'];
		$rules['dokumen_survei.collateral.bpkb.tahun']			= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'date_format:"Y"', 'before:'.date('Y')];
		$rules['dokumen_survei.collateral.bpkb.atas_nama']		= ['required_if:dokumen_survei.collateral.jenis,bpkb'];
		$rules['dokumen_survei.collateral.bpkb.alamat']			= ['required_if:dokumen_survei.collateral.jenis,bpkb'];
		$rules['dokumen_survei.collateral.bpkb.nomor_bpkb']		= ['required_if:dokumen_survei.collateral.jenis,bpkb'];
		$rules['dokumen_survei.collateral.bpkb.nomor_mesin']	= ['required_if:dokumen_survei.collateral.jenis,bpkb'];
		$rules['dokumen_survei.collateral.bpkb.nomor_rangka']	= ['required_if:dokumen_survei.collateral.jenis,bpkb'];
		$rules['dokumen_survei.collateral.bpkb.masa_berlaku_stnk']	= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'date_format:"Y-m-d"'];
		$rules['dokumen_survei.collateral.bpkb.fungsi_sehari_hari']	= ['required_if:dokumen_survei.collateral.jenis,bpkb'];
		$rules['dokumen_survei.collateral.bpkb.faktur']				= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.bpkb.kwitansi_jual_beli']	= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.bpkb.kwitansi_kosong']	= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.bpkb.ktp_an_bpkb']		= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.bpkb.kondisi_kendaraan']	= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'in:baik,cukup_baik,buruk'];
		$rules['dokumen_survei.collateral.bpkb.status_kepemilikan']	= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'in:an_sendiri,an_orang_lain_milik_sendiri,an_orang_lain_dengan_surat_kuasa'];
		$rules['dokumen_survei.collateral.bpkb.asuransi']			= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'in:all_risk,tlo,tidak_ada'];
		$rules['dokumen_survei.collateral.bpkb.harga_taksasi']		= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'numeric'];
		$rules['dokumen_survei.collateral.bpkb.persentasi_bank']	= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'numeric'];
		$rules['dokumen_survei.collateral.bpkb.harga_bank']			= ['required_if:dokumen_survei.collateral.jenis,bpkb', 'numeric'];
		
		$rules['dokumen_survei.collateral.shm.atas_nama_sertifikat']= ['required_if:dokumen_survei.collateral.jenis,shm'];
		$rules['dokumen_survei.collateral.shm.nomor_sertifikat']	= ['required_if:dokumen_survei.collateral.jenis,shm'];
		$rules['dokumen_survei.collateral.shm.alamat']			= ['required_if:dokumen_survei.collateral.jenis,shm'];
		$rules['dokumen_survei.collateral.shm.tipe']			= ['required_if:dokumen_survei.collateral.jenis,shm', 'in:tanah,tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shm.luas_tanah']		= ['required_if:dokumen_survei.collateral.jenis,shm'];
		$rules['dokumen_survei.collateral.shm.panjang_tanah']	= ['required_if:dokumen_survei.collateral.jenis,shm'];
		$rules['dokumen_survei.collateral.shm.lebar_tanah']		= ['required_if:dokumen_survei.collateral.jenis,shm'];
		$rules['dokumen_survei.collateral.shm.luas_bangunan']	= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shm.panjang_bangunan']= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shm.lebar_bangunan']	= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shm.fungsi_bangunan']	= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan', 'in:ruko,rukan,rumah'];
		$rules['dokumen_survei.collateral.shm.bentuk_bangunan']	= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan', 'in:tingkat,tidak_tingkat'];
		$rules['dokumen_survei.collateral.shm.konstruksi_bangunan']	= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan', 'in:permanen,semi_permanen'];
		$rules['dokumen_survei.collateral.shm.lantai_bangunan']		= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan', 'in:keramik,tegel_biasa'];
		$rules['dokumen_survei.collateral.shm.dinding']		= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan', 'in:tembok,semi_tembok'];
		$rules['dokumen_survei.collateral.shm.listrik']		= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shm.air']			= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan', 'in:pdam,sumur'];
		$rules['dokumen_survei.collateral.shm.lain_lain']	= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shm.jalan']		= ['required_if:dokumen_survei.collateral.jenis,shm', 'in:tanah,batu,aspal'];
		$rules['dokumen_survei.collateral.shm.letak_lokasi_terhadap_jalan']	= ['required_if:dokumen_survei.collateral.jenis,shm', 'in:sama,lebih_rendah,lebih_tinggi'];
		$rules['dokumen_survei.collateral.shm.lingkungan']		= ['required_if:dokumen_survei.collateral.jenis,shm', 'in:perumahan,kampung,pertokoan,pasar,perkantoran,industri'];
		$rules['dokumen_survei.collateral.shm.ajb']				= ['required_if:dokumen_survei.collateral.jenis,shm', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.shm.pbb_terakhir']	= ['required_if:dokumen_survei.collateral.jenis,shm', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.shm.imb']				= ['required_if:dokumen_survei.collateral.jenis,shm', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.shm.asuransi']		= ['required_if:dokumen_survei.collateral.jenis,shm', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.shm.njop']			= ['required_if:dokumen_survei.collateral.jenis,shm', 'numeric'];
		$rules['dokumen_survei.collateral.shm.nilai_tanah']		= ['required_if:dokumen_survei.collateral.jenis,shm', 'numeric'];
		$rules['dokumen_survei.collateral.shm.njop_tanah']		= ['required_if:dokumen_survei.collateral.jenis,shm', 'numeric'];
		$rules['dokumen_survei.collateral.shm.nilai_bangunan']	= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan', 'numeric'];
		$rules['dokumen_survei.collateral.shm.njop_bangunan']	= ['required_if:dokumen_survei.collateral.shm.tipe,tanah_dan_bangunan', 'numeric'];
		$rules['dokumen_survei.collateral.shm.persentasi_taksasi']	= ['required_if:dokumen_survei.collateral.jenis,shm', 'numeric'];
		$rules['dokumen_survei.collateral.shm.harga_taksasi']		= ['required_if:dokumen_survei.collateral.jenis,shm', 'numeric'];

		$rules['dokumen_survei.collateral.shgb.atas_nama_sertifikat']	= ['required_if:jenis,collateral'];
		$rules['dokumen_survei.collateral.shgb.nomor_sertifikat']		= ['required_if:dokumen_survei.collateral.jenis,shm'];
		$rules['dokumen_survei.collateral.shgb.masa_berlaku_sertifikat']= ['required_if:jenis,collateral', 'date_format:"Y-m-d"'];
		$rules['dokumen_survei.collateral.shgb.alamat']		= ['required_if:jenis,collateral'];
		$rules['dokumen_survei.collateral.shgb.tipe']		= ['required_if:jenis,collateral', 'in:tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shgb.luas_tanah']	= ['required_if:jenis,collateral'];
		$rules['dokumen_survei.collateral.shgb.panjang_tanah']		= ['required_if:jenis,collateral'];
		$rules['dokumen_survei.collateral.shgb.lebar_tanah']		= ['required_if:jenis,collateral'];
		$rules['dokumen_survei.collateral.shgb.luas_bangunan']		= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shgb.panjang_bangunan']	= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shgb.lebar_bangunan']		= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shgb.fungsi_bangunan']	= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan', 'in:ruko,rukan,rumah'];
		$rules['dokumen_survei.collateral.shgb.bentuk_bangunan']	= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan', 'in:tingkat,tidak_tingkat'];
		$rules['dokumen_survei.collateral.shgb.konstruksi_bangunan']= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan', 'in:permanen,semi_permanen'];
		$rules['dokumen_survei.collateral.shgb.lantai_bangunan']	= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan', 'in:keramik,tegel_biasa'];
		$rules['dokumen_survei.collateral.shgb.dinding']	= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan', 'in:tembok,semi_tembok'];
		$rules['dokumen_survei.collateral.shgb.listrik']	= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shgb.air']		= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan', 'in:pdam,sumur'];
		$rules['dokumen_survei.collateral.shgb.lain_lain']	= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan'];
		$rules['dokumen_survei.collateral.shgb.jalan']		= ['required_if:jenis,collateral', 'in:tanah,batu,aspal'];
		$rules['dokumen_survei.collateral.shgb.letak_lokasi_terhadap_jalan']	= ['required_if:jenis,collateral', 'in:sama,lebih_rendah,lebih_tinggi'];
		$rules['dokumen_survei.collateral.shgb.lingkungan']	= ['required_if:jenis,collateral', 'in:perumahan,kampung,pertokoan,pasar,perkantoran,industri'];
		$rules['dokumen_survei.collateral.shgb.ajb']			= ['required_if:jenis,collateral', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.shgb.pbb_terakhir']	= ['required_if:jenis,collateral', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.shgb.imb']			= ['required_if:jenis,collateral', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.shgb.asuransi']		= ['required_if:jenis,collateral', 'in:ada,tidak_ada'];
		$rules['dokumen_survei.collateral.shgb.njop']			= ['required_if:jenis,collateral', 'numeric'];
		$rules['dokumen_survei.collateral.shgb.nilai_tanah']	= ['required_if:jenis,collateral', 'numeric'];
		$rules['dokumen_survei.collateral.shgb.njop_tanah']		= ['required_if:jenis,collateral', 'numeric'];
		$rules['dokumen_survei.collateral.shgb.nilai_bangunan']	= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan', 'numeric'];
		$rules['dokumen_survei.collateral.shgb.njop_bangunan']	= ['required_if:dokumen_survei.collateral.shgb.tipe,tanah_dan_bangunan', 'numeric'];
		$rules['dokumen_survei.collateral.shgb.persentasi_taksasi']	= ['required_if:dokumen_survei.collateral.jenis,shgb', 'numeric'];
		$rules['dokumen_survei.collateral.shgb.harga_taksasi']		= ['required_if:jenis,collateral', 'numeric'];
		
		// $rules['dokumen_survei.collateral.foto.*.url']			= ['required_if:jenis,collateral', 'url'];
		// $rules['dokumen_survei.collateral.foto.*.keterangan']	= ['required_if:jenis,collateral'];
		
		$data 					= $this->attributes;
		$data['dokumen_survei'] = json_decode($data['dokumen_survei'], true);

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

	public function getDokumenSurveiAttribute()
	{
		$variable 	= json_decode($this->attributes['dokumen_survei'], true);

		if(str_is($this->jenis, 'capacity'))
		{
			$variable['capacity']['penghasilan']['gaji']		= $this->formatMoneyTo($variable['capacity']['penghasilan']['gaji']);
			$variable['capacity']['penghasilan']['pasangan']	= $this->formatMoneyTo($variable['capacity']['penghasilan']['pasangan']);
			$variable['capacity']['penghasilan']['usaha']		= $this->formatMoneyTo($variable['capacity']['penghasilan']['usaha']);
			$variable['capacity']['penghasilan']['lain_lain']	= $this->formatMoneyTo($variable['capacity']['penghasilan']['lain_lain']);
			$variable['capacity']['pengeluaran']['biaya_produksi']		= $this->formatMoneyTo($variable['capacity']['pengeluaran']['biaya_produksi']);
			$variable['capacity']['pengeluaran']['biaya_rumah_tangga']	= $this->formatMoneyTo($variable['capacity']['pengeluaran']['biaya_rumah_tangga']);
			$variable['capacity']['pengeluaran']['biaya_listrik_pdam_telepon']	= $this->formatMoneyTo($variable['capacity']['pengeluaran']['biaya_listrik_pdam_telepon']);
			$variable['capacity']['pengeluaran']['biaya_pendidikan']	= $this->formatMoneyTo($variable['capacity']['pengeluaran']['biaya_pendidikan']);
			$variable['capacity']['pengeluaran']['lain_lain']	= $this->formatMoneyTo($variable['capacity']['pengeluaran']['lain_lain']);
			$variable['capacity']['kemampuan_angsur']			= $this->formatMoneyTo($variable['capacity']['kemampuan_angsur']);
		}

		if(str_is($this->jenis, 'capital'))
		{
			if(isset($variable['capital']['rumah']['angsuran_bulanan']))
			{
				$variable['capital']['rumah']['angsuran_bulanan']	= $this->formatMoneyTo($variable['capital']['rumah']['angsuran_bulanan']);
			}
			$variable['capital']['rumah']['nilai_rumah']			= $this->formatMoneyTo($variable['capital']['rumah']['nilai_rumah']);
			$variable['capital']['kendaraan']['nilai_kendaraan']	= $this->formatMoneyTo($variable['capital']['kendaraan']['nilai_kendaraan']);
			$variable['capital']['usaha']['nilai_aset']				= $this->formatMoneyTo($variable['capital']['usaha']['nilai_aset']);
		}

		if(str_is($this->jenis, 'collateral') && str_is($variable['collateral']['jenis'], 'bpkb'))
		{
			$variable['collateral']['bpkb']['harga_taksasi']		= $this->formatMoneyTo($variable['collateral']['bpkb']['harga_taksasi']);
			$variable['collateral']['bpkb']['harga_bank']			= $this->formatMoneyTo($variable['collateral']['bpkb']['harga_bank']);
			$variable['collateral']['bpkb']['masa_berlaku_stnk']	= $this->formatDateTo($variable['collateral']['bpkb']['masa_berlaku_stnk']);
		}

		if(str_is($this->jenis, 'collateral') && str_is($variable['collateral']['jenis'], 'shm'))
		{
			if(isset($variable['collateral']['shm']['njop_bangunan']))
			{
				$variable['collateral']['shm']['njop_bangunan']		= $this->formatMoneyTo($variable['collateral']['shm']['njop_bangunan']);
			}
			if(isset($variable['collateral']['shm']['njop_bangunan']))
			{
				$variable['collateral']['shm']['nilai_bangunan']	= $this->formatMoneyTo($variable['collateral']['shm']['nilai_bangunan']);
			}
			$variable['collateral']['shm']['njop_tanah']	= $this->formatMoneyTo($variable['collateral']['shm']['njop_tanah']);
			$variable['collateral']['shm']['nilai_tanah']	= $this->formatMoneyTo($variable['collateral']['shm']['nilai_tanah']);
			$variable['collateral']['shm']['harga_taksasi']	= $this->formatMoneyTo($variable['collateral']['shm']['harga_taksasi']);
			
		}

		if(str_is($this->jenis, 'collateral') && str_is($variable['collateral']['jenis'], 'shgb'))
		{
			$variable['collateral']['shgb']['njop_bangunan']	= $this->formatMoneyTo($variable['collateral']['shgb']['njop_bangunan']);
			$variable['collateral']['shgb']['nilai_bangunan']	= $this->formatMoneyTo($variable['collateral']['shgb']['nilai_bangunan']);
			$variable['collateral']['shgb']['njop_tanah']		= $this->formatMoneyTo($variable['collateral']['shgb']['njop_tanah']);
			$variable['collateral']['shgb']['nilai_tanah']		= $this->formatMoneyTo($variable['collateral']['shgb']['nilai_tanah']);
			$variable['collateral']['shgb']['harga_taksasi']	= $this->formatMoneyTo($variable['collateral']['shgb']['harga_taksasi']);			
		}

		return $variable;
	}

	public function getErrorsAttribute()
	{
		return $this->errors;
	}
}
