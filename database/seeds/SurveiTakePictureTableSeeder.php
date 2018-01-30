<?php

use Illuminate\Database\Seeder;
use Thunderlabid\Manajemen\Models\Orang;

use Thunderlabid\Pengajuan\Models\Pengajuan;

use Thunderlabid\Survei\Models\Survei;
use Thunderlabid\Survei\Models\SurveiFoto;
use Thunderlabid\Survei\Models\SurveiDetail;
use Thunderlabid\Survei\Models\AssignedSurveyor;

use Thunderlabid\Survei\Traits\IDRTrait;

use Carbon\Carbon;

class SurveiTakePictureTableSeeder extends Seeder
{
	use IDRTrait;

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$paket_foto_tanah 	= [
			[
				'url'			=> 'http://rumahdijual.org/athumb/b/3/8/big356208.jpg',
				'keterangan'	=> 'Pekarangan',
			],
			[
				'url'			=> 'http://www.balirealproperty.com/photo/property/property-1353644223-tjub144-jual-tanah-di-ubud-08jpg.jpg',
				'keterangan'	=> 'Lokasi Dekat Jalan',
			],
			[
				'url'			=> 'https://anekarumahjogja.files.wordpress.com/2011/06/jual-tanah-jogja4.jpg',
				'keterangan'	=> 'Tanah Garap',
			],
		];
		$paket_foto_rumah 	= [
			[
				'url'			=> 'https://tipsjualrumah13.files.wordpress.com/2011/12/rumah-dijual-murah.jpg',
				'keterangan'	=> 'Tampak Depan',
			],
			[
				'url'			=> 'http://gambar-rumah.com/attachments/bandung/3961277d1448412187-dijual-rumah-minimalis-di-kolmas-cimahi-bandung-tampak-samping.jpg',
				'keterangan'	=> 'Tampak Samping',
			],
			[
				'url'			=> 'http://gambar-rumah.com/attachments/jakarta-selatan/95789d1335353396-dijual-rumah-minimalis-di-bintaro-village-tampak-depan-dan-samping.jpg',
				'keterangan'	=> 'Lokasi Perempatan',
			],
		];
		
		$paket_foto_apartment = [
			[
				'url'			=> 'http://gambar-rumah.com/attachments/bandung/5538357d1463016564-jual-apartemen-baru-murah-bandung-utara-condotel-dago-clove-screenshot_2015-10-23-17-29-36.jpg',
				'keterangan'	=> 'Jacuzzi',
			],
			[
				'url'			=> 'http://rumahdijual.org/athumb/5/e/c/big3428868.jpg',
				'keterangan'	=> 'Denah Apartment',
			],
			[
				'url'			=> 'http://media.equityapartments.com/images/c_crop,x_0,y_0,w_1920,h_1080/c_fill,w_1920,h_1080/q_80/4206-28/the-kelvin-apartments-exterior.jpg',
				'keterangan'	=> 'Tampak Depan Lingkungan Apartment',
			],
		];


		$paket_foto_roda_2 	= [
			[
				'url'			=> 'https://lh3.googleusercontent.com/-sdfmrIX9xpA/VWZ8OAAsBRI/AAAAAAAAMdQ/SZfSz1MudRs/s2560/1432779830565.jpeg',
				'keterangan'	=> 'Foto Jaminan',
			],
			[
				'url'			=> 'https://img.olx.biz.id/A5B8/42424/308242424_3_644x461_dijual-motor-nmax-abs-th-2016-bln-09-km-baru-3-ribuan-yamaha.jpg',
				'keterangan'	=> 'Foto Jaminan',
			],
			[
				'url'			=> 'http://img.olx.biz.id/A197/41367/358876314_1_644x461_dijual-motor-yamaha-vixion-keluaran-tahun-2015-palembang-kota.jpg',
				'keterangan'	=> 'Foto Jaminan',
			],
		];

		$paket_foto_roda_3 	= [
			[
				'url'			=> 'http://otoboy.com/wp-content/uploads/2016/03/Harga-Motor-Roda-Tiga-Viar.jpg',
				'keterangan'	=> 'Foto Jaminan',
			],
			[
				'url'			=> 'https://storage.jualo.com/original/1343808/dijual-bajaj-bbg-tvs-lain-lain-1343808.jpg',
				'keterangan'	=> 'Foto Jaminan',
			],
			[
				'url'			=> 'https://i.pinimg.com/originals/73/87/d9/7387d9c18c4e24bebabd703706afc5a6.jpg',
				'keterangan'	=> 'Foto Jaminan',
			],
		];

		$paket_foto_roda_4 	= [
			[
				'url'			=> 'http://www.promosuzuki.com/wp-content/uploads/2015/11/keunggulan-mobil-carry.jpg',
				'keterangan'	=> 'Foto Jaminan',
			],
			[
				'url'			=> 'http://www.asapmobil.com/wp-content/uploads/2015/06/Harga-Mobil-Terbaru-Hyundai-Creta-Indonesia.jpg',
				'keterangan'	=> 'Foto Jaminan',
			],
			[
				'url'			=> 'http://1.bp.blogspot.com/-23MAGluLmKQ/VbJXdYG8rLI/AAAAAAAACqQ/AI5bcQxA1xI/s1600/Genio941.jpg',
				'keterangan'	=> 'Foto Jaminan',
			],
		];

		$paket_foto_roda_6 	= [
			[
				'url'			=> 'http://1.bp.blogspot.com/-mL_QUzgho94/VkoAA-HkIgI/AAAAAAAAP4U/0xkFAtD4DVw/s1600/Scania%2BK410%2BSatria%2BMuda%2B8.jpg',
				'keterangan'	=> 'Foto Jaminan',
			],
			[
				'url'			=> 'https://s.kaskus.id/images/2013/06/04/91010_20130604054053.jpg',
				'keterangan'	=> 'Foto Jaminan',
			],
			[
				'url'			=> 'http://4.bp.blogspot.com/-OK10G3m3BO0/Vpy1GMPLCaI/AAAAAAAACZo/Vg2zlFLYq40/s1600/fuso%2Bwing.jpg',
				'keterangan'	=> 'Foto Jaminan',
			],
		];

		//SIMPAN JAMINAN
		$ids 			= ['1801.1801.0002.0101'];
		$pengajuan 		= Pengajuan::wherein('id', $ids)->get();

		foreach ($pengajuan as $key => $value) 
		{
			///COLLATERAL///
			$s_survei_c5 	= [];
			foreach ($value->jaminan as $key_j => $value_j) 
			{
				switch ($value_j->jenis) {
					case 'shm':
						if($value_j['dokumen_jaminan']['shm']['tipe']=='tanah')
						{
							$s_survei_foto[$key_j] 	= $this->survei_foto($paket_foto_tanah);
						}
						else
						{
							$s_survei_foto[$key_j] 	= $this->survei_foto($paket_foto_rumah);
						}
						$s_detail 		= SurveiDetail::where('jenis', 'collateral')->where('dokumen_survei->collateral->shm->nomor_sertifikat', $value_j['dokumen_jaminan']['shm']['nomor_sertifikat'])->first();
						break;
					case 'shgb':
						$s_survei_foto[$key_j] 	= $this->survei_foto($paket_foto_apartment);
						$s_detail 		= SurveiDetail::where('jenis', 'collateral')->where('dokumen_survei->collateral->shgb->nomor_sertifikat', $value_j['dokumen_jaminan']['shgb']['nomor_sertifikat'])->first();
						break;
					default:
						$s_detail 	= SurveiDetail::where('jenis', 'collateral')->where('dokumen_survei->collateral->bpkb->nomor_bpkb', $value_j['dokumen_jaminan']['bpkb']['nomor_bpkb'])->first();

						if($value_j['dokumen_jaminan']['shm']['tipe']=='roda_6')
						{
							$s_survei_foto[$key_j] 	= $this->survei_foto($paket_foto_roda_6);
						}
						elseif($value_j['dokumen_jaminan']['shm']['tipe']=='roda_4')
						{
							$s_survei_foto[$key_j] 	= $this->survei_foto($paket_foto_roda_4);
						}
						elseif($value_j['dokumen_jaminan']['shm']['tipe']=='roda_3')
						{
							$s_survei_foto[$key_j] 	= $this->survei_foto($paket_foto_roda_3);
						}
						else
						{
							$s_survei_foto[$key_j] 	= $this->survei_foto($paket_foto_roda_2);
						}
						break;
				}

				$s_survei_foto[$key_j]['survei_detail_id']	= $s_detail['id'];
				SurveiFoto::create($s_survei_foto[$key_j]);
			}
		}
	}

	private function survei_foto($paket_foto)
	{
		$survei_foto['arsip_foto'][0]	= $paket_foto[rand(0,2)];
		$survei_foto['arsip_foto'][1]	= $paket_foto[rand(0,2)];
		$survei_foto['arsip_foto'][2]	= $paket_foto[rand(0,2)];
		$survei_foto['arsip_foto'][3]	= $paket_foto[rand(0,2)];

		return $survei_foto;
	}
}
