@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('carbon', 'Carbon\Carbon')

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ isset($html['title']) ? $html['title'] : 'GO-KREDIT.COM' }}</title>

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
					<h4>LAPORAN KAS HARIAN</h4>
					<h4>PADA PENUTUPAN KAS, TANGGAL</h4>
					<h4>{{ $dday->format('d/m/Y') }}</h4>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<table class="w-100">
				<tbody>
					<tr>
						<td>Neraca</td>
						<td>{{ $dbefore->format('d/m/Y') }}</td>
						<td class="text-right">{{ $idr->formatmoneyto($balance) }}</td>
					</tr>
					<tr>
						<td>Penerimaan Kas</td>
						<td></td>
						<td class="text-right">{{ $idr->formatmoneyto($in) }}</td>
					</tr>
					<tr>
						<td>TOTAL</td>
						<td></td>
						<td class="text-right">{{ $idr->formatmoneyto($balance + $in) }}</td>
					</tr>
					<tr>
						<td>Pengeluaran Kas</td>
						<td></td>
						<td class="text-right">{{ $idr->formatmoneyto($out) }}</td>
					</tr>
					<tr>
						<td>NERACA</td>
						<td>{{ $dday->format('d/m/Y') }}</td>
						<td class="text-right">{{ $idr->formatmoneyto($balance + $in + $out) }}</td>
					</tr>
				</tbody>
			</table>

			<div class="clearfix">&nbsp;</div>
			<table class="table table-bordered">
				<thead>
					<tr class="text-center">
						<th>Diperiksa</th>
						<th>Disetujui</th>
						<th>Diterima</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><br/><br/><br/></td>
						<td><br/><br/><br/></td>
						<td><br/><br/><br/></td>
					</tr>
				</tbody>
			</table>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-12 col-sm-12 col-md-12 text-left">
					<h4>BERITA ACARA PEMERIKSAAN KAS</h4>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-6 text-left">
					Pada hari ini, __________________________
				</div>
				<div class="col-6 text-right">
					Pukul ________ WIB
				</div>
			</div>
			<div class="row">
				<div class="col-12 text-left">
					Kami yang bertanda tangan dibawah ini telah melakukan pemeriksaan Fisik Kas sebagai berikut :
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-12 text-left mb-1">
					<strong>Uang Kertas</strong>
				</div>
			</div>
			<table class="table table-bordered">
				<thead>
					<tr class="text-center">
						<th>Pecahan</th>
						<th>Jumlah</th>
						<th>Nominal</th>
					</tr>
				</thead>
				<tbody>
					<tr class="text-right">
						<td>Rp 100.000</td>
						<td>______ lembar</td>
						<td></td>
					</tr>
					<tr class="text-right">
						<td>Rp 50.000</td>
						<td>______ lembar</td>
						<td></td>
					</tr>
					<tr class="text-right">
						<td>Rp 20.000</td>
						<td>______ lembar</td>
						<td></td>
					</tr>
					<tr class="text-right">
						<td>Rp 10.000</td>
						<td>______ lembar</td>
						<td></td>
					</tr>
					<tr class="text-right">
						<td>Rp 5.000</td>
						<td>______ lembar</td>
						<td></td>
					</tr>
					<tr class="text-right">
						<td>Rp 2.000</td>
						<td>______ lembar</td>
						<td></td>
					</tr>
					<tr class="text-right">
						<td>Rp 1.000</td>
						<td>______ lembar</td>
						<td></td>
					</tr>
					<tr class="text-right">
						<td colspan="2"><strong>Subtotal</strong></td>
						<td></td>
					</tr>
				</tbody>
			</table>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-12 text-left mb-1">
					<strong>Uang Logam</strong>
				</div>
			</div>
			<table class="table table-bordered">
				<thead>
					<tr class="text-center">
						<th>Pecahan</th>
						<th>Jumlah</th>
						<th>Nominal</th>
					</tr>
				</thead>
				<tbody>
					<tr class="text-right">
						<td>Rp 1.000</td>
						<td>______ keping</td>
						<td></td>
					</tr>
					<tr class="text-right">
						<td>Rp 500</td>
						<td>______ keping</td>
						<td></td>
					</tr>
					<tr class="text-right">
						<td>Rp 200</td>
						<td>______ keping</td>
						<td></td>
					</tr>
					<tr class="text-right">
						<td>Rp 100</td>
						<td>______ keping</td>
						<td></td>
					</tr>
					<tr class="text-right">
						<td colspan="2"><strong>Subtotal</strong></td>
						<td></td>
					</tr>
				</tbody>
			</table>
		
			<table class="table">
				<tbody>
					<tr>
						<td colspan="2" class="text-right border-0">TOTAL</td>
						<td class="text-right border-0"></td>
					</tr>
					<tr>
						<td colspan="2" class="text-right border-0">SALDO NERACA</td>
						<td class="text-right border-0">{{$idr->formatmoneyto($balance + $in + $out)}}</td>
					</tr>
					<tr>
						<td colspan="2" class="text-right border-0">SELISIH KAS</td>
						<td class="text-right border-0"></td>
					</tr>
				</tbody>
			</table>

			<div class="row">
				<div class="col-12 text-left">
					Terbilang :
					<hr/>
				</div>
			</div>

			<div class="row">
				<div class="col-12 text-left">
					Catatan :
					<hr/>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>

			<table class="table table-bordered">
				<thead>
					<tr class="text-center">
						<th>Diperiksa</th>
						<th>Disetujui</th>
						<th>Diterima</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><br/><br/><br/></td>
						<td><br/><br/><br/></td>
						<td><br/><br/><br/></td>
					</tr>
				</tbody>
			</table>
		</div>
	</body>
</html>