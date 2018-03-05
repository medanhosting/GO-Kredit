<?php

use Illuminate\Database\Seeder;
use Thunderlabid\Manajemen\Models\Orang;
use Thunderlabid\Manajemen\Models\Kantor;
use Thunderlabid\Manajemen\Models\PengaturanScopes;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use Thunderlabid\Manajemen\Models\MobileApi;

use Carbon\Carbon;

class LiveTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('m_kantor')->truncate();
		DB::table('m_orang')->truncate();
		DB::table('m_pengaturan_scopes')->truncate();
		DB::table('m_penempatan_karyawan')->truncate();
		DB::table('m_mobile_api')->truncate();
		
		//
		//BASIC HOLDING SURABAYA
		Kantor::create(['nama'   => 'Kantor Holding', 'alamat' => ['alamat' => 'Letjen S. Parman 21', 'rt' => '002', 'rw' => '006', 'kelurahan' => 'Purwantoro', 'kecamatan' => 'Blimbing', 'kota' => 'Malang', 'provinsi' => 'Jawa Timur'], 'geolocation' => ['latitude' => -7.954028, 'longitude' => 112.6363283], 'telepon' => '0341 404900', 'tipe' => 'holding']);
		
		//pusat malang
		Kantor::create(['nama'   => 'BPR Artha Makmur', 'alamat' => ['alamat' => 'Jl. Letjen Sutoyo 102B', 'rt' => '002', 'rw' => '006', 'kelurahan' => 'Blimbing', 'kecamatan' => 'Blimbing', 'kota' => 'Malang', 'provinsi' => 'Jawa Timur'], 'geolocation' => ['latitude' => -7.9786394, 'longitude' => 112.5615702], 'telepon' => '0341 777777', 'tipe' => 'pusat', 'jenis' => 'bpr']);

		/////////////	
		//KOMISARIS//
		/////////////

		//BASIC KOMISARIS
		Orang::create(['nama'   => 'KOMISARIS', 'email' => 'chelsy@go-kredit.com', 'password' => 'admin123']);

		//BASIC KOMISARIS
		PenempatanKaryawan::create(['kantor_id' => Kantor::orderby('created_at', 'asc')->first()['id'], 'orang_id' => Orang::orderby('created_at', 'asc')->first()['id'], 'role' => 'superadmin', 'scopes' => ['permohonan', 'survei', 'analisa', 'putusan', 'validasi', 'surat_peringatan', 'denda', 'angsuran', 'jaminan', 'tagihan', 'restitusi', 'tunggakan', 'holding', 'akun', 'kantor', 'karyawan', 'passcode', 'audit', 'pencairan', 'realisasi', 'operasional', 'keuangan', 'kas', '*.nominatif_unlimitted', '*.hari_unlimitted'], 'policies' => '', 'tanggal_masuk' => Carbon::now()->format('d/m/Y H:i')]);

		PenempatanKaryawan::create(['kantor_id' => Kantor::orderby('created_at', 'asc')->skip(1)->take(1)->first()['id'], 'orang_id' => Orang::orderby('created_at', 'asc')->first()['id'], 'role' => 'superadmin', 'scopes' => ['permohonan', 'survei', 'analisa', 'putusan', 'validasi', 'surat_peringatan', 'denda', 'angsuran', 'jaminan', 'tagihan', 'restitusi', 'tunggakan', 'holding', 'akun', 'kantor', 'karyawan', 'passcode', 'audit', 'pencairan', 'realisasi', 'operasional', 'keuangan', 'kas', '*.nominatif_unlimitted', '*.hari_unlimitted'], 'policies' => '', 'tanggal_masuk' => Carbon::now()->format('d/m/Y H:i')]);

		$roles 	= [
			[
				'role' 		=> 'ao',
				'scopes' 	=> ['permohonan'],
			],
			[
				'role' 		=> 'surveyor',
				'scopes' 	=> ['survei', 'survei.hari_gte_3'],
			],
			[
				'role' 		=> 'analis',
				'scopes' 	=> ['analisa', 'analisa.hari_e_0'],
			],
			[
				'role' 		=> 'pimpinan',
				'scopes' 	=> ['operasional', 'putusan', 'putusan.nominatif_lte_10000000', 'keuangan', 'validasi', 'assign', 'surat_peringatan', 'restitusi.nominatif_gt_1000000', '*.hari_e_0'],
			],
			[
				'role' 		=> 'kabag_operasional',
				'scopes' 	=> ['operasional', 'jaminan',  'restitusi', '*.hari_e_0', 'keuangan', 'realisasi'],
			],
			[
				'role' 		=> 'kabag_kredit',
				'scopes' 	=> ['tunggakan', '*.hari_e_0'],
			],
			[
				'role' 		=> 'komisaris',
				'scopes' 	=> ['operasional', 'putusan', 'putusan.nominatif_gt_10000000', 'keuangan', 'validasi', 'restitusi.nominatif_gt_1000000', 'holding', 'akun', 'kantor', 'karyawan', 'passcode', 'audit', '*.hari_e_0'],
			],
			[
				'role' 		=> 'kasir',
				'scopes' 	=> ['pencairan', 'angsuran', 'denda', 'keuangan', '*.hari_e_0', 'kas'],
			],
			[
				'role' 		=> 'kolektor',
				'scopes' 	=> ['tagihan', 'tagihan.hari_gte_3'],
			],
		];

		foreach ($roles as $k => $v) {
			//BASIC ROLE
			Orang::create(['nama'   => ucwords($v['role']), 'email' => $v['role'].'@go-kredit.com', 'password' => 'admin123', 'alamat' => ['alamat' => 'Jl. Letjen Sutoyo 102B', 'rt' => '002', 'rw' => '006', 'kelurahan' => 'Blimbing', 'kecamatan' => 'Blimbing', 'kota' => 'Malang', 'provinsi' => 'Jawa Timur'], 'telepon' => '031 248673']);
			
			//BASIC ROLE
			PenempatanKaryawan::create(['kantor_id' => Kantor::orderby('created_at', 'asc')->skip(1)->take(1)->first()['id'], 'orang_id' => Orang::where('email', $v['role'].'@go-kredit.com')->first()['id'], 'role' => $v['role'], 'scopes' => $v['scopes'], 'policies' => '', 'tanggal_masuk' => Carbon::now()->format('d/m/Y H:i')]);
		}

		//BASIC MOBILE
		MobileApi::create(['key' => 'THUNDERCUTE', 'secret' => 'AWAWAWLAND', 'tipe' => 'customer', 'versi' => 1]);
		MobileApi::create(['key' => 'THUNDERSWAG', 'secret' => 'WKWKWKLAND', 'tipe' => 'admin', 'versi' => 1]);
	}
}
