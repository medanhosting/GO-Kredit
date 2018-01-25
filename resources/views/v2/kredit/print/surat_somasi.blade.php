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
				<div class="col-9">
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-2">Nomor</div>
						<div class="col-10">
							<p class="mb-1" style="border-bottom: 1px dotted #ccc">: {{$surat['nomor_surat']}}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-2">Periphal</div>
						<div class="col-10">
							<p class="mb-1" style="border-bottom: 1px dotted #ccc">: {{ucwords(str_replace('_',' ', $surat['tag']))}}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-2">Kepada Yth.</div>
						<div class="col-10">
							<p class="mb-1">: Bapak/Ibu {{$surat['kredit']['nasabah']['nama']}}</p>
							<p>{{implode(' ', $surat['kredit']['nasabah']['alamat'])}}</p>
						</div>
					</div>
				</div>
				<div class="col-3 text-right">
					<p>{{ $kantor_aktif['alamat']['kota'] }}, {{$tanggal_surat->format('d/m/Y')}}</p>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-12">
					<p>Dengan Hormat,</p>
					<p class="text-justify">
						Kami <span class="border-dotted">{{$kantor_aktif['nama']}}</span> beralamat di <span class="border-dotted">{{implode(' ', $kantor_aktif['alamat'])}}</span>, dengan ini memberikan somasi pertama kepada Bapak/Ibu/Saudara sehubungan hal-hal sebagai berikut:
					</p>
					<p class="text-justify">
						Sesuai dengan Surat Perjanjian Kredit  Nomor : <strong class="border-dotted">{{$surat['nomor_kredit']}}</strong> tanggal realisasi <strong class="border-dotted">{{ $carbon::createfromformat('d/m/Y H:i', $surat['kredit']['tanggal'])->format('d/m/Y') }}</strong>, terhitung pertanggal <strong class="border-dotted">{{ $tanggal_surat->format('d/m/Y') }}</strong>, Bapak/Ibu/saudara mempunyai kewajiban yang harus diselesaikan adalah sebagai berikut : 
					</p>

					<p><strong><u>Rincian yang harus dibayar :</u></strong></p>

					<div class="row">
						<div class="col-3">
							- Tunggakan Angsuran ({{$t_tunggakan->jumlah_tunggakan/2}})
						</div>
						<div class="col-2 text-right">
							{{$idr->formatmoneyto($t_tunggakan->tunggakan)}}&emsp;&nbsp;
						</div>
					</div>
					@if($after)
					@if($after['pokok'] * 1 > 0)
					<div class="row">
						<div class="col-3">
							- Pokok
						</div>
						<div class="col-2 text-right">
							{{$idr->formatmoneyto($after['pokok'])}}&emsp;&nbsp;
						</div>
					</div>
					@endif
					@if($after['bunga'] * 1 > 0)
					<div class="row">
						<div class="col-3">
							- Bunga
						</div>
						<div class="col-2 text-right">
							{{$idr->formatmoneyto($after['bunga'])}}&emsp;&nbsp;
						</div>
					</div>
					@endif
					@endif
					@if($before)
					@if($before['denda'] * 1 > 0)
					<div class="row">
						<div class="col-3">
							- Denda
						</div>
						<div class="col-2 text-right">
							{{$idr->formatmoneyto($before['denda'])}}&emsp;&nbsp;
						</div>
					</div>
					@endif
					@endif
					@if($middle)
					@if($middle['titipan'] * 1 > 0)
					<div class="row">
						<div class="col-3">
							- Titipan
						</div>
						<div class="col-2 text-right">
							{{$idr->formatmoneyto($middle['titipan'])}}&emsp;&nbsp;
						</div>
					</div>
					@endif
					@endif
					<div class="row">
						<div class="col-3">
						</div>
						<div class="col-2 text-right border-dotted">
						</div>
					</div>
					<div class="row">
						<div class="col-3">
							- Total
						</div>
						<div class="col-2 text-right">
							{{$idr->formatmoneyto($t_tunggakan->tunggakan + ($after['pokok'] * 1) + ($after['bunga'] * 1) + ($before['denda'] * 1) - ($middle['titipan'] * 1))}}&emsp;&nbsp;
						</div>
					</div>
					<div class="clearfix">&nbsp;</div>

					<p class='text-justify'>
						Pada saat ini Bapak/Ibu/Saudara telah ingkar janji (wanprestasi) karena terbukti tidak membayar utang sesuai perjanjian yang dibuat, sedangkan uapaya penagihan sudah dilakukan. <strong>Oleh karena itu kami memberikan somasi / peringatan pertama kepada Bapak/Ibu/Saudara supaya segera datang ke koperasi untuk membayar utang dan atau tunggakan tersebut di atas dalam waktu selambat-lambatnya pada tanggal <span class="border-dotted">{{ $tanggal_surat->adddays(6)->format('d/m/Y') }}</span></strong>.
					</p>

					<p class='text-justify'>
						Demi menghindari timbulnya tindakan hukum lebih lanjut, maka kami meminta kepada Bapak/Ibu/Saudara supaya mengindahkan dan melaksanakan isi somasi ini (membayar lunas).
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
							<p class="mb-0"><strong><u>{{ $pimpinan['orang']['nama'] }}</u></strong></p>
							<p><strong>Pimpinan</strong></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>