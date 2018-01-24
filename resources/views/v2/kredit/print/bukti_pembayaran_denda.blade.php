@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('carbon', 'Carbon\Carbon')

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>BUKTI PEMBAYARAN DENDA</title>

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
			<div class="row">
				<div class="col text-center">
					<h4 class="mb-1"><strong>BUKTI PEMBAYARAN DENDA</strong></h4>
				</div>
			</div>
			<hr class="mt-3 mb-2" style="border-size: 2px;">
			<div class="clearfix">&nbsp;</div>
			<table class="w-100">
				<tr class="align-top">
					<td style="width: 12.5%">AC / SPK</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">{{$angsuran['nomor_kredit']}}</p>
					</td>
					<td style="width: 12.5%">Nama</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $angsuran['kredit']['nasabah']['nama'] }}
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td colspan="6">
						<div class="clearfix">&nbsp;</div>
						<div class="clearfix">&nbsp;</div>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th class="text-center" style="width: 5%;">#</th>
									<th class="text-left" style="width: 22%;">Deskripsi</th>
									<th class="text-right" style="width: 20%;">Denda</th>
									<th class="text-right">Restitusi</th>
									<th class="text-right">Subtotal</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($angsuran['details'] as $k => $v)
									<tr>
										<td>{{ $loop->iteration }}</td>
										<td>Denda ke- {{ $v['nth'] }}</td>
										<td class="text-right">{{ $idr->formatMoneyTo($v['denda']) }}</td>
										<td class="text-right">{{ $idr->formatMoneyTo($v['restitusi_denda']) }}</td>
										<td class="text-right">{{ $idr->formatMoneyTo($v['subtotal']) }}</td>
									</tr>
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<td class="text-right" colspan="4"><h5><strong>Total</strong></h5></td>
									<td class="text-right"><h5><strong>{{ $angsuran['jumlah'] }}</strong></h5></td>
								</tr>
							</tfoot>
						</table>
						<div class="clearfix">&nbsp;</div>
					</td>
				</tr>
			</table>			
			<div class="row">
				<div class="col-6">
					<table class="table table-bordered w-100 mt-4">
						<thead class="thead-light">
							<tr>
								<th class="text-center p-2 w-25">Dibuat</th>
								<th class="text-center p-2 w-25">Diperiksa</th>
								<th class="text-center p-2 w-25">Disetujui</th>
								<th class="text-center p-2 w-25">Dibayar</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 35px;">&nbsp;</td>
								<td style="padding: 35px;">&nbsp;</td>
								<td style="padding: 35px;">&nbsp;</td>
								<td style="padding: 35px;">&nbsp;</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-6">
					<table class="table w-50 text-center ml-auto mr-5 mt-2" style="height: 220px;">
						<tbody>
							<tr>
								<td class="border-0">{{ $kantor_aktif['alamat']['kota'] }}, {{ $tanggal_bayar->format('d/m/Y') }}</td>
							</tr>
							<tr>
								<td class="border-0">
									<p class="border border-left-0 border-right-0 border-bottom-0">{{$angsuran['kredit']['nasabah']['nama']}}</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>