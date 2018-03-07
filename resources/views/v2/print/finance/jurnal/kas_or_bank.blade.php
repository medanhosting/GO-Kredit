@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('carbon', 'Carbon\Carbon')

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ isset($html['title']) ? $html['title'] . ' | GO-KREDIT.COM'  : 'GO-KREDIT.COM' }}</title>

		<link rel="stylesheet" href="{{ mix('css/app.css') }}">

		<!-- Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<style type="text/css">
			.border-dotted {
				border-bottom: 1px dotted #555;
			}
			table, .border, hr {
				border-color: #555 !important;
			}
		</style>
	</head>
	<body>
		<div class="container-fluid" style="width: 21cm;height: 29.7cm;">
			<div class="clearfix">&nbsp;</div>
			<div class="row align-items-center">
				<div class="col-10 text-center position-relative">
					<span class="float-right position-absolute" style="right: 0; margin-right: 45px;">
						Reg: <strong style="color: #812519; font-size: 15px;">JK-{{$id}}{{$tanggal->format('dmy')}}</strong>
					</span>
					<h4 class="mb-1">
						<strong>JURNAL {{ strtoupper($type) }}</strong>
					</h4>
					<hr class="mb-5" style="border-width: 2px !important;" />
				</div>
				<div class="col-2">
					<h1 class="text-center px-4 border mb-1" style="border-width: 2px !important;">
						<strong>{{ $tanggal->format('d') }}</strong>
					</h1>
					<p class="text-center mb-0">{{ $tanggal->format("M 'y") }}</p>
				</div>
			</div>
			<div class="row">
				@foreach ($akun as $k => $v)
					<div class="col-6">
						<p class="mb-1"><strong>JURNAL {{ strtoupper($v['akun']) }} MASUK</strong></p>
						<table class="table table-bordered">
							<thead class="thead-light">
								<tr>
									<th>Perkiraan</th>
									<th>Uraian</th>
									<th class="text-right">Jumlah</th>
								</tr>
							</thead>
							<tbody>
								@php $total = 0 @endphp
								@foreach ($v['subakun'] as $k2 => $v2)
									<tr>
										<td>{{ $v2['nomor_perkiraan'] }}</td>
										<td>{{ $v2['akun'] }}</td>
										@php 
											$subtotal 	= array_sum(array_column($v2['detailsin'], 'amount'));
											$total 		= $total + $subtotal
										@endphp
										<td class="text-right">{{ $idr->formatMoneyTo($subtotal) }}</td>
									</tr>
								@endforeach
								<tr>
									<td colspan="2">Total</td>
									<td class="text-right" style="background-color: #ddd;">{{ $idr->formatMoneyTo($total) }}</td>
								</tr>
								<tr>
									<td colspan="3">{{ ucwords($idr->terbilang($total)) }} Rupiah</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-6">
						<p class="mb-1"><strong>JURNAL {{ strtoupper($v['akun']) }} KELUAR</strong></p>
						<table class="table table-bordered">
							<thead class="thead-light">
								<tr>
									<th>Perkiraan</th>
									<th>Uraian</th>
									<th class="text-right">Jumlah</th>
								</tr>
							</thead>
							<tbody>
								@php $total = 0 @endphp
								@foreach ($v['subakun'] as $k2 => $v2)
									<tr>
										<td>{{ $v2['nomor_perkiraan'] }}</td>
										<td>{{ $v2['akun'] }}</td>
										@php 
											$subtotal 	= abs(array_sum(array_column($v2['detailsout'], 'amount')));
											$total 		= $total + $subtotal
										@endphp
										<td class="text-right">{{ $idr->formatMoneyTo($subtotal) }}</td>
									</tr>
								@endforeach
								<tr>
									<td colspan="2">Total</td>
									<td class="text-right" style="background-color: #ddd;">{{ $idr->formatMoneyTo($total) }}</td>
								</tr>
								<tr>
									<td colspan="3">{{ ucwords($idr->terbilang($total)) }} Rupiah</td>
								</tr>
							</tbody>
						</table>
					</div>
				@endforeach
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="row">
				<div class="col-6">
					<p class="mb-1"><strong>{{$kantor_aktif['nama']}}</strong></p>
					<p>{{implode(' ', $kantor_aktif['alamat'])}}</p>
				</div>
				<div class="col-6 text-right">
					<ul class="list-inline">
						<li class="list-inline-item border" style="width: 60px; height: 60px;">&nbsp;</li>
						<li class="list-inline-item border" style="width: 60px; height: 60px;">&nbsp;</li>
						<li class="list-inline-item border" style="width: 60px; height: 60px;">&nbsp;</li>
					</ul>
				</div>
			</div>
		</div>
	</body>
</html>