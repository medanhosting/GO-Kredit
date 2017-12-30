@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
@inject('carbon', 'Carbon\Carbon')

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>SURAT PERNYATAAN SEBAGAI PENJAMIN</title>

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
						<div class="col-7">{{$kantor_aktif['id']}} / {{$putusan['pengajuan_id']}}</div>
					</div>
					<div class="row justify-content-end">
						<div class="col-2">Tanggal</div>
						<div class="col-7">
							{{ $putusan['tanggal'] }}
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col text-center">
					<h4 class="mb-1"><strong>BUKTI REALISASI KREDIT</strong></h4>
				</div>
			</div>
			<hr class="mt-1 mb-2" style="border-size: 2px;">
			<p class="mb-3">Sudah terima dari {{ strtoupper($kantor_aktif['nama']) }} sejumlah uang dibawah ini untuk direalisasi kredit:</p>
			<table>
				<tr class="align-top">
					<td style="width: 12.5%">Nomor Rekening</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">gak tau variabelnya</p>
					</td>
					<td style="width: 12.5%">No. SPK</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">gak tau variabelnya</p>
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
					<td style="width: 12.5%">Usaha</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">gak tau variabelnya</p>
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
					<td style="width: 12.5%">Jenis Pinjaman</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">gak tau variabelnya</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Jaminan</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">gak tau variabelnya</p>
					</td>
					<td style="width: 12.5%">Nama AO</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2 text-capitalize">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $putusan['pengajuan']['ao']['nama'] }}
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Nominal</td>
					<td style="width: 1%">:</td>
					<td class="pl-2 pr-2 w-75" colspan="4">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ ($putusan['plafon_pinjaman']) }}
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Terbilang</td>
					<td style="width: 1%">:</td>
					<td class="pl-2 pr-2 text-capitalize" colspan="4">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $idr->terbilang($idr->formatMoneyFrom($putusan['plafon_pinjaman'])) }}
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Jangka Waktu</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $putusan['jangka_waktu'] }} Bulan
						</p>
					</td>
					<td style="width: 12.5%">Suku Bunga</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $putusan['suku_bunga'] }} %
						</p>
					</td>
				</tr>
				<tr class="align-top">
					<td style="width: 12.5%">Angsuran per Bulan</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $putusan['pengajuan']['kemampuan_angsur'] }}
						</p>
					</td>
					<td style="width: 12.5%">Tgl Jatuh Tempo per Bulan</td>
					<td style="width: 1%">:</td>
					<td class="w-25 pl-2 pr-2">
						<p class="mb-2" style="border-bottom: 1px dotted #ccc">
							{{ $putusan['tanggal'] }} <-- tolong diparsing ke tanggal aja
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
					<table class="table w-50 text-center ml-auto mr-5" style="height: 220px;">
						<tbody>
							<tr>
								<td class="border-0">{{ $kantor_aktif['alamat']['kota'] }}, {{ $carbon->now()->format('d/m/Y') }}</td>
							</tr>
							<tr>
								<td class="border-0">
									<p class="border border-left-0 border-right-0 border-bottom-0">Nama terang dan tanda tangan</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>