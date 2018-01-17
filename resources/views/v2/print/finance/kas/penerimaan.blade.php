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
				<h4><strong>LAPORAN PENERIMAAN KAS</strong></h4>
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
					<!-- <th class="text-left align-middle" rowspan="2">Uraian</th> -->
					<th class="text-center align-middle" colspan="2">Pokok</th>
					<th class="text-center align-middle" colspan="2">Bunga</th>
					<th class="text-right align-middle" rowspan="2">Denda</th>
					<th class="text-right align-middle" rowspan="2">Titipan</th>
					<th class="text-right align-middle" rowspan="2">Lain - Lain</th>
				</tr>
				<tr>
					<th class="text-right">PA</th>
					<th class="text-right">PT</th>
					<th class="text-right">PA</th>
					<th class="text-right">PT</th>
				</tr>
			</thead>
			<tbody>
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
						<!-- <td class="text-left">
							Angsuran Kredit Nomor {{$v['nomor_kredit']}}
						</td> -->
						<td class="text-right">
							@if(str_is($v['kredit']['jenis_pinjaman'], 'pa'))
							{{$idr->formatMoneyTo($v['pokok'])}}
							@else
							{{$idr->formatMoneyTo(0)}}
							@endif
						</td>
						<td class="text-right">
							@if(str_is($v['kredit']['jenis_pinjaman'], 'pt'))
							{{$idr->formatMoneyTo($v['pokok'])}}
							@else
							{{$idr->formatMoneyTo(0)}}
							@endif
						</td>
						<td class="text-right">
							@if(str_is($v['kredit']['jenis_pinjaman'], 'pa'))
							{{$idr->formatMoneyTo($v['bunga'])}}
							@else
							{{$idr->formatMoneyTo(0)}}
							@endif
						</td>
						<td class="text-right">
							@if(str_is($v['kredit']['jenis_pinjaman'], 'pt'))
							{{$idr->formatMoneyTo($v['bunga'])}}
							@else
							{{$idr->formatMoneyTo(0)}}
							@endif
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo($v['denda'])}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo($v['titipan'])}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo(0)}}
							<!-- {{$idr->formatMoneyTo($v['potongan'] + $v['potongan_denda'])}} -->
						</td>
					</tr>
				@empty
				@endforelse
				<tfoot>
					<tr>
						<td colspan="9">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">JUMLAH PENERIMAAN HARI INI</td>
						<td class="text-right">{{$idr->formatMoneyTo(array_sum(array_column($angsuran->toArray(), 'subtotal')))}}</td>
						<td colspan="2">JUMLAH ANGSURAN JATUH TEMPO</td>
						<td class="text-right">{{$idr->formatMoneyTo($total_jatuh_tempo)}}</td>
						<th>Dibukukan</th>
						<th>Diperiksa</th>
						<th>Dibuat</th>
					</tr>
					<tr>
						<td colspan="2">SALDO KEMARIN TANGGAL {{$today->subdays(1)->format('d/m/Y')}}</td>
						<td class="text-right">{{$idr->formatMoneyTo($total_money_yesterday)}}</td>
						<td colspan="2">JUMLAH PELUNASAN DIPERCEPAT DAN PENURUNAN POKOK</td>
						<td class="text-right">{{$idr->formatMoneyTo($total_pelunasan)}}</td>
						<td rowspan="4">&nbsp;</td>
						<td rowspan="4">&nbsp;</td>
						<td rowspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">JUMLAH KONTROL</td>
						<td></td>
						<td colspan="2">TOTAL ANGSURAN</td>
						<td class="text-right">{{$idr->formatMoneyTo($total_angsuran)}}</td>
					</tr>
					<tr>
						<td colspan="2" rowspan="2">{{$kantor_aktif['nama']}}<br/>
						<small>{{implode(' ', $kantor_aktif['alamat'])}}</small>
						</td>
						<td rowspan="2">SALDO KAS HARI INI<br/> {{$today->adddays(1)->format('d/m/Y')}}</td>
						<td rowspan="2" class="text-right"><h3>{{$idr->formatMoneyTo($total_money + $total_money_yesterday )}}</h3></td>
						<td colspan="2" rowspan="2"><i>Terbilang : {{ucwords($idr->terbilang(abs($total_money + $total_money_yesterday) ))}} Rupiah</i></td>
					</tr>
				</tfoot>
			</tbody>
		</table>
	</div>
</body>
</html>