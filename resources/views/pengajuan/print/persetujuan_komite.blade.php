<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>PUTUSAN KOMITE KREDIT</title>

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

		<!-- Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

		<!-- Styles -->
		<style>

		</style>
	</head>
	<body>
		<div class="container" style="width: 21cm;height: 29.7cm; ">
			<div class="clearfix">&nbsp;</div>
			
			<div class="row text-center">
				<div class="col-xs-12">
					<h3>PUTUSAN KOMITE KREDIT</h3>
				</div> 
			</div> 

			<div class="clearfix">&nbsp;</div>

			<div style="font-size:11px;">
				<div class="row text-justify">
					<div class="col-xs-12 text-left">
						<p>Disetujui untuk diberikan fasilitas pinjaman @if($realisasi['isi']['putusan']['is_baru']) baru @else perpanjangan @endif</p>
					</div>
				</div>
				<div class="row text-justify">
					<div class="col-xs-3 text-left">
						<p>Plafon Pinjaman</p>
					</div>
					<div class="col-xs-9 text-left">
						<p>{{$realisasi['isi']['putusan']['plafon_pinjaman']}}</p>
					</div>
				</div>
				<div class="row text-justify">
					<div class="col-xs-3 text-left">
						<p>Suku Bunga</p>
					</div>
					<div class="col-xs-9 text-left">
						<p>{{$realisasi['isi']['putusan']['suku_bunga']}} %</p>
					</div>
				</div>
				<div class="row text-justify">
					<div class="col-xs-3 text-left">
						<p>Jangka Waktu</p>
					</div>
					<div class="col-xs-9 text-left">
						<p>{{$realisasi['isi']['putusan']['jangka_waktu']}}</p>
					</div>
				</div>
				<div class="row text-justify">
					<div class="col-xs-3 text-left">
						<p>Biaya Tambahan</p>
					</div>
					<div class="col-xs-9 text-left">
						<p>{{$realisasi['isi']['putusan']['provisi']}} provisi, {{$realisasi['isi']['putusan']['administrasi']}} administrasi,
						{{$realisasi['isi']['putusan']['legal']}} legal</p>
					</div>
				</div>
				<div class="row text-justify">
					<div class="col-xs-12 text-left">
						Jaminan
					</div>
					<ol>
						@foreach($realisasi['isi']['putusan']['survei']['collateral'] as $k => $v)
						<div class="col-xs-6 text-left">
							<li>
								{{strtoupper($v['dokumen_survei']['collateral']['jenis'])}}<br/>
								@foreach($v['dokumen_survei']['collateral'][$v['dokumen_survei']['collateral']['jenis']] as $k2 => $v2)
									@if(in_array($k2, ['nomor_sertifikat', 'atas_nama_sertifikat', 'nilai_tanah', 'nilai_bangunan', 'harga_taksasi', 'nomor_bpkb', 'merk', 'tipe', 'atas', 'atas_nama', 'tahun', 'harga_bank']))
										{{str_replace('_',' ',$k2)}} {{str_replace('_',' ',$v2)}} <br/>
									@endif
								@endforeach
							</li>
						</div>
						@endforeach
					</ol>
				</div>
				<div class="clearfix">&nbsp;</div>
				<div class="row text-justify">
					<div class="col-xs-12 text-left">
						{{strtoupper($realisasi['isi']['putusan']['putusan'])}} dengan catatan {{strtoupper($realisasi['isi']['putusan']['catatan'])}}
					</div>
				</div>
				<div class="clearfix">&nbsp;</div>
				<div class="row text-center" style="padding:5px;">
					<div class="col-12" style="background-color:#aaa;border:1px solid; padding:5px;">
						KOMITE KREDIT
					</div>
				</div>
				<div class="row text-center" style="padding:5px;">
					<div class="col-xs-3" style="background-color:#aaa;border:1px solid; padding:5px;">
						ANALIS KREDIT
					</div>
					<div class="col-xs-3" style="background-color:#aaa;border:1px solid; border-left:none;padding:5px;">
						KABAG KREDIT
					</div>
					<div class="col-xs-3" style="background-color:#aaa;border:1px solid; border-left:none;padding:5px;">
						PIMPINAN
					</div>
					<div class="col-xs-3" style="background-color:#aaa;border:1px solid; border-left:none;padding:5px;">
						KOMISARIS
					</div>
					<div class="col-xs-3" style="border:1px solid;border-top:none;padding:5px;">
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
					</div>
					<div class="col-xs-3" style="border:1px solid;border-top:none;border-left:none;padding:5px;">
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
					</div>
					<div class="col-xs-3" style="border:1px solid;border-top:none;border-left:none;padding:5px;">
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
					</div>
					<div class="col-xs-3" style="border:1px solid;border-top:none;border-left:none;padding:5px;">
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
					</div>
				</div>
				<div class="clearfix">&nbsp;</div>
				<div class="row text-center" style="padding:5px;">
					<div class="col-12" style="background-color:#aaa;border:1px solid; padding:5px;">
						CHECKLIST DOKUMEN UMUM
					</div>
				</div>
				<div class="row text-center" style="padding:5px;">
					<div class="col-xs-6" style="background-color:#aaa;border:1px solid; padding:5px;">
						IDENTITAS NASABAH DAN JAMINAN KREDIT
					</div>
					<div class="col-xs-6" style="background-color:#aaa;border:1px solid; border-left:none;padding:5px;">
						DOKUMEN PENGIKAT KREDIT
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6">
						@foreach($realisasi['isi']['putusan']['checklists']['objek'] as $k3 => $v3)
							@if($v3!='cadangkan')
								<div class="row">
									<div class="col-xs-8 text-left">
										{{ucwords(str_replace('_',' ',$k3))}}
									</div>
									<div class="col-xs-4 text-right">
										{{ucwords(str_replace('_',' ',$v3))}}
									</div>
								</div>
							@endif
						@endforeach
					</div>
					<div class="col-xs-6">
						@foreach($realisasi['isi']['putusan']['checklists']['pengikat'] as $k3 => $v3)
							@if($v3!='cadangkan')
								<div class="row">
									<div class="col-xs-8 text-left">
										{{ucwords(str_replace('_',' ',$k3))}}
									</div>
									<div class="col-xs-4 text-right">
										{{ucwords(str_replace('_',' ',$v3))}}
									</div>
								</div>
							@endif
						@endforeach
					</div>
				</div>
				
				<div class="clearfix">&nbsp;</div>
				<div class="row text-center">
					<div class="col-xs-12" style="background-color:#aaa;border:1px solid; padding:5px;">
						DIPERIKSA
					</div> 
				</div> 
				<div class="clearfix">&nbsp;</div>
				<div class="row">
					<div class="col-xs-2 text-left">
						Tanggal 
					</div>
					<div class="col-xs-4 text-right">
						{{$realisasi['isi']['putusan']['tanggal']}}
					</div>
				</div>
				<div class="row">
					<div class="col-xs-2 text-left">
						Nama 
					</div>
					<div class="col-xs-4 text-right">
						{{$realisasi['isi']['putusan']['pembuat_keputusan']['nama']}}
					</div>
				</div>
				<div class="row">
					<div class="col-xs-2 text-left">
						Paraf 
					</div>
					<div class="col-xs-4 text-right">
						
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
