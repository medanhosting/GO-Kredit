<?php

use Illuminate\Database\Seeder;
use Thunderlabid\Manajemen\Models\Orang;
use Thunderlabid\Manajemen\Models\Kantor;
use Thunderlabid\Manajemen\Models\PengaturanScopes;
use Thunderlabid\Manajemen\Models\PenempatanKaryawan;

use Thunderlabid\Manajemen\Models\MobileApi;

use Carbon\Carbon;

class ManajemenTableSeeder extends Seeder
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
		//BASIC HOLDING
		Kantor::create(['nama'   => 'Kantor Holding', 'alamat' => ['alamat' => 'Jl. Letjen Sutoyo 102A', 'rt' => '002', 'rw' => '006', 'kelurahan' => 'Blimbing', 'kecamatan' => 'Blimbing', 'kota' => 'Malang', 'provinsi' => 'Indonesia'], 'geolocation' => ['latitude' => 7, 'longitude' => 102], 'telepon' => '0341 888888', 'tipe' => 'holding']);
		
		Kantor::create(['nama'   => 'Koperasi Artha Makmur', 'alamat' => ['alamat' => 'Jl. Letjen Sutoyo 102B', 'rt' => '002', 'rw' => '006', 'kelurahan' => 'Blimbing', 'kecamatan' => 'Blimbing', 'kota' => 'Malang', 'provinsi' => 'Indonesia'], 'geolocation' => ['latitude' => 7, 'longitude' => 102], 'telepon' => '0341 777777', 'tipe' => 'pusat']);

		//BASIC ADMIN
		Orang::create(['nama'   => 'Chelsy Mooy', 'email' => 'chelsy@thunderlab.id', 'password' => 'adminadmin']);

		//BASIC SCOPES
		$this->scopes();

		//BASIC PENEMPATAN
		PenempatanKaryawan::create(['kantor_id' => Kantor::first()['id'], 'orang_id' => Orang::first()['id'], 'role' => 'komisaris', 'scopes' => ['pengajuan','permohonan', 'survei', 'analisa', 'keputusan', 'setujui', 'tolak', 'realisasi', 'expire'], 'policies' => '', 'tanggal_masuk' => Carbon::now()->format('d/m/Y H:i')]);
		
		PenempatanKaryawan::create(['kantor_id' => Kantor::skip(1)->first()['id'], 'orang_id' => Orang::first()['id'], 'role' => 'pimpinan', 'scopes' => ['pengajuan','permohonan', 'survei', 'analisa', 'keputusan', 'setujui', 'tolak', 'realisasi', 'expire'], 'policies' => ['setujui' => ['max:10000000']], 'tanggal_masuk' => Carbon::now()->format('d/m/Y H:i')]);
		
		//BASIC MOBILE
		MobileApi::create(['key' => 'THUNDERCUTE', 'secret' => 'AWAWAWLAND', 'tipe' => 'customer', 'versi' => 1]);
		MobileApi::create(['key' => 'THUNDERSWAG', 'secret' => 'WKWKWKLAND', 'tipe' => 'admin', 'versi' => 1]);
	}

	private function scopes()
	{
		//iterasi 1
		$scopes             = [[
			'id'			=> 1,
			'scope'         => 'pengajuan',  
			'policies'      => [],  
			'icon'          => null,  
			'scope_id'      => null,
			'features'		=> [[
				'id'			=> 2,
				'scope'         => 'permohonan',  
				'policies'      => [],  
				'icon'          => 'fa fa-please',  
				'scope_id'      => 1,
				'features'		=> [],
			],[
				'id'			=> 3,
				'scope'         => 'survei',  
				'policies'      => [],  
				'icon'          => 'fa fa-survei',  
				'scope_id'      => 1,
				'features'		=> [],
			],[
				'id'			=> 4,
				'scope'         => 'analisa',  
				'policies'      => [],  
				'icon'          => 'fa fa-analisa',  
				'scope_id'      => 1,
				'features'		=> [],
			],[
				'id'			=> 5,
				'scope'         => 'keputusan',  
				'policies'      => [],  
				'icon'          => 'fa fa-keputusan',  
				'scope_id'      => 1,
				'features'		=> [[
					'id'			=> 6,
					'scope'         => 'setujui',  
					'policies'      => ['max:10000000'],  
					'icon'          => 'fa fa-check',  
					'scope_id'      => 5,
					'features'		=> [[
						'id'			=> 7,
						'scope'         => 'realisasi',  
						'policies'      => [],  
						'icon'          => 'fa fa-check',  
						'scope_id'      => 6,
						'features'		=> [],
					],[
						'id'			=> 8,
						'scope'         => 'expire',  
						'policies'      => [],  
						'icon'          => 'fa fa-uncheck',  
						'scope_id'      => 6,
						'features'		=> [],
					]],
				],[
					'id'			=> 9,
					'scope'         => 'tolak',  
					'policies'      => [],  
					'icon'          => 'fa fa-uncheck',  
					'scope_id'      => 5,
					'features'		=> [],
				]],
			]]  
		]];

		$store 	= $this->storeScopes($scopes);
	}


	private function storeScopes($scopes)
	{
		foreach ($scopes as $key => $value) 
		{
			$new_val 	= $value;
			unset($new_val['features']);

			$scope 	= new PengaturanScopes;
			$scope->fill($new_val);
			$scope->save();
			
			if((array)$value['features'])
			{
				$store = $this->storeScopes($value['features']);
			}
			elseif(!isset($scopes[$key+1]))
			{
				return true;
			}
		}
	}
}
