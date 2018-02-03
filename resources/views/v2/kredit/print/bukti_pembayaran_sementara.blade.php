@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('carbon', 'Carbon\Carbon')

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>BUKTI ANGSURAN SEMENTARA</title>

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
					<h4 class="mb-1"><strong>BUKTI ANGSURAN SEMENTARA</strong></h4>
				</div>
			</div>
			<hr class="mt-3 mb-2" style="border-size: 2px;">
			<div class="clearfix">&nbsp;</div>
			<table class="w-100">
				<tr class="align-top">
					<td style="width: 15%">Telah Diterima Dari</td>
					<td style="width: 1%">:</td>
					<td class="w-50 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
						@if($angsuran['penagihan'])
							{{$angsuran['penagihan']['penerima']['nama']}}
						@else
							{{$angsuran['kredit']['nasabah']['nama']}}
						@endif
						&nbsp;</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 15%">No Rek / SPK</td>
					<td style="width: 1%">:</td>
					<td class="w-50 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc"> &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; / {{$angsuran['morph_reference_id']}}</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 15%">Jumlah</td>
					<td style="width: 1%">:</td>
					<td class="w-50 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc"> {{$angsuran['jumlah']}}</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 15%">Terbilang</td>
					<td style="width: 1%">:</td>
					<td class="w-50 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc"> {{ucwords($idr->terbilang($idr->formatMoneyFrom($angsuran['jumlah'])))}} Rupiah </p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 15%">Untuk Pembayaran</td>
					<td style="width: 1%">:</td>
					<td class="w-50 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							@foreach($angsuran['details'] as $k => $v)
								@if ($loop->last)
									{{$v['deskripsi']}}
								@else
									{{$v['deskripsi']}}, 
								@endif
							@endforeach
						</p>
					</td>
				</tr>
			</table>			
			<div class="row">
				<div class="col-6">
					<table class="table table-bordered w-100 mt-4">
						<thead class="thead-light">
							<tr>
								<th class="text-center p-2 w-25">Diperiksa</th>
								<th class="text-center p-2 w-25">Disetujui</th>
								<th class="text-center p-2 w-25">Diterima</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 35px;">&nbsp;</td>
								<td style="padding: 35px;">&nbsp;</td>
								<td style="padding: 35px;">&nbsp;</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-6">
					<table class="table w-100 text-center ml-auto mr-5 mt-2" style="height: 220px;">
						<tbody>
							<tr>
								<td class="border-0">&nbsp;<br/>Petugas</td>
								<td class="border-0">{{ $kantor_aktif['alamat']['kota'] }}, {{ $tanggal_bayar->format('d/m/Y') }}<br/>Penyetor</td>
							</tr>
							<tr>
								<td class="border-0">
									<p class="border border-left-0 border-right-0 border-bottom-0">{{$angsuran['penagihan']['karyawan']['nama']}}</p>
								</td>
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