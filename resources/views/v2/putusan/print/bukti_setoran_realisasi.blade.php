@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('carbon', 'Carbon\Carbon')

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>BUKTI REALISASI</title>

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
					<ul class="list-unstyled fa-ul mb-2">
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
						<div class="col-7">{{$notabayar['nomor_faktur']}}</div>
					</div>
					<div class="row justify-content-end">
						<div class="col-2">Tanggal</div>
						<div class="col-7">
							{{$tanggal_s}}
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col text-center">
					<h4 class="mb-1"><strong>BUKTI SETORAN KREDIT</strong></h4>
				</div>
			</div>
			<hr class="mt-1 mb-2" style="border-size: 2px;">
			<table>
				<tr class="align-top">
					<td colspan="3">&nbsp;</td>
					<td rowspan="5" class="w-25 pl-2 pr-2">
						<table class="table table-bordered">
							<thead class="thead-light">
								<tr>
									<th class="text-center p-2">Uraian</th>
									<th class="text-center p-2">Jumlah</th>
								</tr>
							</thead>
							<tbody>
								@foreach($notabayar['details'] as $k => $v)
								<tr>
									<td class="p-1">{{$v['description']}}</td>
									<td class="p-1 text-right">{{$v['amount']}}</td>
								</tr>
								@endforeach
							<tfoot>
								<tr>
									<th class="p-1">Total</th>
									<th class="p-1 text-right">{{ $notabayar['jumlah'] }}</th>
								</tr>
							</tfoot>
						</table>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Jenis Setoran</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							Potongan Realisasi
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Nomor Rekening</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">&nbsp;</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Nama</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $putusan['pengajuan']['nasabah']['nama'] }}
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Alamat</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2 text-capitalize">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ strtolower(implode(' ', $putusan['pengajuan']['nasabah']['alamat'])) }}
						</p>
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
					<p class="mb-0 pt-2" style="border-bottom: 1px dotted #ccc">
						<i>Terbilang : {{ ucwords($idr->terbilang($idr->formatMoneyFrom($notabayar['jumlah']))) }} Rupiah</i>
					</p>
					<table class="table w-50 text-center ml-auto mr-5" style="height: 200px;">
						<tbody>
							<tr>
								<td class="border-0">{{ $kantor_aktif['alamat']['kota'] }}, {{ $tanggal_s }}</td>
							</tr>
							<tr>
								<td class="border-0">
									<p class="border border-left-0 border-right-0 border-bottom-0">
									{{ $putusan['pengajuan']['nasabah']['nama'] }}
									</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>