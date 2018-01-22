@inject('idr', 'App\Service\UI\IDRTranslater')
@inject('tanggal', 'App\Service\UI\TanggalTranslater')
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
		<div class="row">
			<div class="col-6">
				<h4><strong>LAPORAN PENGELUARAN KAS</strong></h4>
			</div>
			<div class="col-6 text-right">
				{{ $carbon->now()->format('d/m/Y') }}
			</div>
		</div>
		<table class="table table-bordered" style="font-size:10px;">
			<thead>
				<tr class="text-center">
					<th class="text-left align-middle" rowspan="2">No</th>
					<th class="text-left align-middle" rowspan="2">Bukti</th>
					<th class="text-left align-middle" rowspan="2">Account</th>
					<th class="text-center align-middle" rowspan="2">Pinjaman Angsuran</th>
					<th class="text-center align-middle" rowspan="2">Pinjaman Tetap</th>
					<th class="text-right align-middle" rowspan="2">Biaya Operasional</th>
					<th class="text-right align-middle" rowspan="2">Biaya Non Operasional</th>
					<th class="text-right align-middle" rowspan="2">Lain - Lain</th>
				</tr>
			</thead>
			<tbody>
				@php $lua = null @endphp
				@forelse($angsuran as $k => $v)
					
					<tr class="text-center">
						<td class="text-left">
							{{$loop->iteration}}
						</td>
						<td class="text-left">
							<a href="{{route('kredit.show', ['id' => $v['kredit']['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'angsuran'])}}">
							{{$v['nomor_faktur']}}
							</a>
						</td>
						<td class="text-right">
							
						</td>
						<td class="text-right">
							@if(str_is($v['kredit']['jenis_pinjaman'], 'pa'))
							{{$v['jumlah']}}
							@else
							{{$idr->formatMoneyTo(0)}}
							@endif
						</td>
						<td class="text-right">
							@if(str_is($v['kredit']['jenis_pinjaman'], 'pt'))
							{{$v['jumlah']}}
							@else
							{{$idr->formatMoneyTo(0)}}
							@endif
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo(0)}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo(0)}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo(0)}}
						</td>
					</tr>
				@empty
				@endforelse
				<tfoot>
					<tr>
						<td colspan="8">&nbsp;</td>
					</tr>
					<tr>
						<td class="align-middle" colspan="2" rowspan="3">SALDO KAS HARI INI {{$today->format('d/m/Y')}}</td>
						<td class="text-right align-middle" rowspan="3">{{$idr->formatMoneyTo($total_money)}}</td>
						<td class="text-right align-middle" colspan="2" rowspan="3"><i>Terbilang : {{ucwords($idr->terbilang(abs($total_money)))}} Rupiah</i></td>
						<th>Dibukukan</th>
						<th>Diperiksa</th>
						<th>Dibuat</th>
					</tr>
					<tr>
						<td rowspan="2">&nbsp;</td>
						<td rowspan="2">&nbsp;</td>
						<td rowspan="2">&nbsp;</td>
					</tr>
				</tfoot>
			</tbody>
		</table>
	</div>
</body>
</html>