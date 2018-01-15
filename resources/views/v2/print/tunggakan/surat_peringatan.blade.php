@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('carbon', 'Carbon\Carbon')

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ isset($html['title']) ? $html['title'] : 'Go-Kredit.com' }}</title>

		<link rel="stylesheet" href="{{ mix('css/app.css') }}">

		<!-- Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<style type="text/css">
			.border-dotted {
				border-bottom: 1px dotted #555;
			}
		</style>
	</head>
	<body>
		<div class="container-fluid" style="width: 21cm;height: 29.7cm; ">
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-12 text-left">
					<h3 class="mb-0"><strong>{{ strtoupper($kantor_aktif['nama']) }}</strong></h3>
					<ul class="list-unstyled mb-1">
						<li>{{ implode(' ', $kantor_aktif['alamat']) }}</li>
						<li>Telepon : {{ $kantor_aktif['telepon'] }}</li>
					</ul>
					<hr class="mt-0 mb-1" style="border-top: 4px solid #444 !important;">
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-6">
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-4">Nomor</div>
						<div class="col-8">
							<p class="mb-1" style="border-bottom: 1px dotted #ccc">: &nbsp;</p>
						</div>
					</div>
					<div class="row">
						<div class="col-4">Periphal</div>
						<div class="col-8">
							<p class="mb-1" style="border-bottom: 1px dotted #ccc">: &nbsp;</p>
						</div>
					</div>
					<div class="row">
						<div class="col-4">Sifat</div>
						<div class="col-8">
							<p class="mb-1" style="border-bottom: 1px dotted #ccc">: &nbsp;</p>
						</div>
					</div>
				</div>
				<div class="col-6 text-right">
					<p>Malang, 20 Januari 2018</p>
					<div class="row justify-content-end">
						<div class="col-9 text-center">
							<p class="mb-1">Kepada</p>
							<p class="mb-1">Yth. Bapak/Ibu {{ isset($name) ? $name : 'nama - gak tau variable' }}</p>
							<p>Di-tempat</p>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-12">
					<p>Dengan Hormat,</p>
					<p class="text-justify">
						Sehubungan dengan Surat Perjanjian Kredit  Nomor : <strong class="border-dotted">{{ isset($nomor_kredit) ? $nomor_kredit : 'Nomor Kredit - gak tau variable' }}</strong class="border-dotted"> atas nama Bapak/Ibu <strong class="border-dotted">{{ isset($name) ? $name : 'Nama - gak tau variable' }}</strong>
						yang telah menunggak sebanyak 1 (satu) kali angsuran terhitung sejak tanggal <strong class="border-dotted">{{ isset($tanggal) ? $tanggal : 'tanggal - gak tau variable' }}</strong> sampai dengan hari ini, 
						dengan total tunggakan sebesar <strong class="border-dotted">{{ isset($tunggakan) ? $tunggakan : 'tunggakan - gak tau variable' }}</strong> (<strong class="border-dotted">{{ isset($terbilang) ? $terbilang : 'terbilang - gak tau variable' }}</strong>).
					</p>
					<p class="text-justify">
						Kami mengharapkan informasi dari Bapak/Ibu atas alasan keterlambatan pembayaran angsuran ini serta rencana pembayaran yang akan dilakukan. 
						Untuk menghubungi kami, Bapak/Ibu dapat langsung menelepon di nomor <strong class="border-dotted">{{ isset($nomor_kantor) ? $nomor_kantor : 'No kantor - gak tau variable' }}</strong> ke bagian Administrasi Collection atau datang langsung ke kantor selama jam kerja
					</p>
					<p class="text-justify">
						Demikian surat pemberitahuan ini kami sampaikan dan atas perhatian serta kerjasama Bapak/Ibu kami sampaikan Terima Kasih.
					</p>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-4">
					<div class="row justify-content-start">
						<div class="col-12 text-center">
							<p>Diterima Oleh</p>
							<div class="clearfix">&nbsp;</div>
							<div class="clearfix">&nbsp;</div>
							<div class="clearfix">&nbsp;</div>
							<p>( <strong>{{ isset($diterima_oleh) ? $diterma_oleh : 'diterima oleh - gak tau variable' }}</strong> )</p>
						</div>
					</div>
				</div>
				<div class="col-7 text-right">
					<div class="row justify-content-end">
						<div class="col-12 text-center">
								<p>{{ $kantor_aktif['nama'] }}</p>
								<div class="clearfix">&nbsp;</div>
								<div class="clearfix">&nbsp;</div>
								<div class="clearfix">&nbsp;</div>
								<div class="clearfix"><u>{{ isset($nama_pimpinan) ? $nama_pimpinan : 'nama pimpinan - gak tau variable' }}</u></div>
								<p><strong>Pimpinan</strong></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>