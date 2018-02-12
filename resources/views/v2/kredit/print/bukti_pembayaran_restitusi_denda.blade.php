@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('carbon', 'Carbon\Carbon')

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>BUKTI ANGSURAN KREDIT</title>

		<link rel="stylesheet" href="{{ mix('css/app.css') }}">

		<!-- Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
	<body>
		<div class="container-fluid" style="width: 21cm;height: 29.7cm; ">
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-6 text-left">
					<h3 class="mb-2">{{strtoupper($kantor_aktif['nama'])}}</h3>
					<ul class="list-unstyled fa-ul">
						<li>
							<i class="fa fa-building-o fa-li" style="margin-top: .2rem;"></i>
							{{ implode(' ', $kantor_aktif['alamat']) }}
						</li>
						<li>
							<i class="fa fa-phone fa-li" style="margin-top: .2rem;"></i>
							{{ $kantor_aktif['telepon'] }}
						</li>
					</ul>
				</div>
				<div class="col-6 text-right">
					<div class="row justify-content-end">
						<div class="col-2">Nomor</div>
						<div class="col-7">{{$angsuran['nomor_faktur']}}</div>
					</div>
					<div class="row justify-content-end">
						<div class="col-2">Tanggal</div>
						<div class="col-7">
							{{ $tanggal_bayar->format('d/m/Y') }}
						</div>
					</div>
				</div>
			</div>
			<div class="row bg-dark">
				<div class="col text-center">
					<h4 class="mb-0 p-1 text-light">PERMOHONAN KERINGANAN DENDA</h4>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<table class="w-100">
				<tr class="align-top">
					<td style="width: 12.5%">Nama Debitur</td>
					<td style="width: 1.5%">:</td>
					<td class="pl-2 pr-2" style="width: 36%;">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $angsuran['kredit']['nasabah']['nama'] }}
						</p>
					</td>
					<td style="width: 12.5%">AC / SPK</td>
					<td style="width: 1.5%">:</td>
					<td class="pl-2 pr-2" style="width: 36%;">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;/{{ $angsuran['kredit']['nomor_kredit'] }}
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Alamat</td>
					<td style="width: 1.5%">:</td>
					<td class="pl-2 pr-2" style="width: 36%;">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc;">
							{{implode(' ', $angsuran['kredit']['nasabah']['alamat'])}}
						</p>
					</td>
				</tr>
			</table>
			<div class="clearfix">&nbsp;</div>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="text-center" style="width: 15%;">Tunggakan</th>
						<th class="text-right" style="width: 30%;">Jumlah</th>
						<th class="text-left" style="width: 45%;">Alasan</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Denda</td>
						<td class="text-right">{{$idr->formatMoneyTo($angsuran['denda'] + $angsuran['abstotal'])}}</td>
						<td class="text-left" rowspan="5">
							{{$angsuran['restitusi']['alasan']}}
						</td>
					</tr>
					<tr>
						<td>Total</td>
						<td class="text-right">{{$idr->formatMoneyTo($angsuran['denda'] + $angsuran['abstotal'])}}</td>
					</tr>

					<tr>
						<td>Kesanggupan Bayar</td>
						<td class="text-right">{{$idr->formatMoneyTo($angsuran['denda'])}}</td>
					</tr>
				</tbody>
			</table>		
			<div class="row bg-dark">
				<div class="col text-center">
					<h4 class="mb-0 p-1 text-light">PERSETUJUAN KERINGANAN</h4>
				</div>
			</div>

			<div class="clearfix">&nbsp;</div>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="text-center" style="width: 15%;">Nominal Persetujuan</th>
						<th class="text-right align-middle" style="width: 30%;">
							@if($angsuran['abstotal'] > 1000000)
								{{$idr->formatMoneyto($angsuran['abstotal'])}}
							@endif
						</th>
						<th class="text-right align-middle" style="width: 30%;">
							@if($angsuran['abstotal'] <= 1000000)
								{{$idr->formatMoneyto($angsuran['abstotal'])}}
							@endif
						</th>
						<th class="text-right align-middle" style="width: 30%">
							&nbsp;
						</th>
					</tr>
				</thead>
				<tbody>
					<tr style="height: 20px;">
						<td class="">Tanda Tangan</td>
						<td class="text-center">Komisaris</td>
						<td class="text-center">Pimpinan</td>
						<td class="text-center">Bag. Kredit</td>
					</tr>
					<tr style="height: 20px;">
						<td class="">Disposisi</td>
						<td class="text-center"></td>
						<td class="text-center">{{$angsuran['karyawan']['nama']}}</td>
						<td class="text-center">{{$angsuran['restitusi']['karyawan']['nama']}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</body>
</html>