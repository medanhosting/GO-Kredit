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
		<div class="container-fluid" style="width: 21cm;height: 29.7cm;">
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-12 text-center">
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
							<p class="mb-1" style="border-bottom: 1px dotted #ccc">: {{ isset($nomor_surat) ? $nomor_surat : 'nomor surat - gak tau variable' }}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-4">Periphal</div>
						<div class="col-8">
							<p class="mb-1" style="border-bottom: 1px dotted #ccc">: {{ isset($periphal) ? $periphal : 'periphal - gak tau variable' }}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-4">Kepada Yth</div>
						<div class="col-8">
							<p class="mb-1" style="border-bottom: 1px dotted #ccc">: {{ isset($nama) ? $nama : 'nama - gak tau variablenya' }}</p>
							<p class="mb-1" style="border-bottom: 1px dotted #ccc">&nbsp; {{ isset($alamat) ? $alamat : 'alamat - gak tau variablenya' }}</p>
						</div>
					</div>
				</div>
				<div class="col-6 text-right">
					<p>{{ isset($tanggal_surat) ? $tanggal_surat : 'tanggal surat - gak tau variablenya' }}</p>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-12">
					<p>Dengan Hormat,</p>
					<p class="text-justify">
						Kami <span class="border-dotted">{{ isset($nomor_kredit) ? $nomor_kredit : 'Nama kantor - gak tau variable' }}</span> beralamat di beralamat di <span class="border-dotted">{{ isset($alamat_kredit) ? $alamat_kredit : 'alamat kantor - gak tau variable' }}</span>, dengan ini memberikan somasi pertama kepada Bapak/Ibu/Saudara sehubungan hal-hal sebagai berikut:
					</p>
					<p class="text-justify">
						Sesuai dengan Surat Perjanjian Kredit  Nomor : <strong class="border-dotted">{{ isset($nomor_kredit) ? $nomor_kredit : 'Nomor Kredit - gak tau variable' }}</strong> tanggal realisasi <strong class="border-dotted">{{ isset($tanggal_realisasi) ? $tanggal_realisasi : 'Tanggal realisasi - gak tau variable' }}</strong>, terhitung pertanggal <strong class="border-dotted">{{ isset($tanggal_terhitung) ? $tanggal_terhitung : 'Tanggal terhitung - gak tau variable' }}</strong>, Bapak/Ibu/saudara mempunyai kewajiban yang harus diselesaikan adalah sebagai berikut : 
					</p>

					<p><strong><u>Rincian yang harus dibayar :</u></strong></p>

					<ul>
						<li>list biaya</li>
						<li>list biaya</li>
					</ul>

					<p class='text-justify'>
						Pada saat ini Bapak/Ibu/Saudara telah ingkar janji (wanprestasi) karena terbukti tidak membayar utang sesuai perjanjian yang dibuat, sedangkan uapaya penagihan sudah dilakukan. <strong>Oleh karena itu kami memberikan somasi / peringatan pertama kepada Bapak/Ibu/Saudara supaya segera datang ke koperasi untuk membayar utang dan atau tunggakan tersebut di atas dalam waktu selambat-lambatnya pada tanggal <span class="border-dotted">{{ isset($tanggal_deadline) ? $tanggal_deadline : 'Tanggal deadline - gak tau variable' }}</span></strong>
					</p>

					<p class='text-justify'>
						Demi menghindari timbulnya tindakan hukum lebih lanjut, maka kami meminta kepada Bapak/Ibu/Saudara supaya mengindahkan dan melaksanakan isi somasi ini (membayar lunas)
					</p>

					<p class='text-justify'>
						Demikian segala tanggapan hendaknya disampaikan langsung kepada pihak koperasi. Atas perhatiannya kami ucapkan terima kasih.
					</p>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="clearfix">&nbsp;</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-4 text-left">
					<div class="row">
						<div class="col-12 text-left">
							<p class="mb-1"><strong>Hormat Kami,</strong></p>
							<p><strong>{{ $kantor_aktif['nama'] }}</strong></p>
							<div class="clearfix">&nbsp;</div>
							<div class="clearfix">&nbsp;</div>
							<div class="clearfix">&nbsp;</div>
							<p class="mb-0"><strong><u>{{ isset($nama_pimpinan) ? $nama_pimpinan : 'nama pimpinan - gak tau variable' }}</u></strong></p>
							<p><strong>Pimpinan</strong></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>