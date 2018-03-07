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
	<div class="container-fluid" style="height: 21cm;width: 29.7cm;">
		<div class="clearfix">&nbsp;</div>
		<div class="row">
			<div class="col-6">
				<h4><strong>TUTUP KAS PENERIMAAN UANG TUNAI</strong></h4>
			</div>
			<div class="col-6 text-right">
				{{ $tanggal->format('d/m/Y') }}
			</div>
		</div>
		<table class="table table-bordered" style="font-size:10px;">
			<thead>
				<tr class="text-center">
					<th class="text-left align-middle" rowspan="2" style="width:2%;">No</th>
					<th class="text-left align-middle" rowspan="2" style="width:18%;">Bukti</th>

					<th class="text-center align-middle" rowspan="2" style="width:8%;">Provisi</th>
					<th class="text-center align-middle" rowspan="2" style="width:8%;">Adm</th>
					<th class="text-center align-middle" rowspan="2" style="width:8%;">Legal</th>

					<th class="text-center align-middle" colspan="2" style="width:16%;">Pokok</th>
					<th class="text-center align-middle" colspan="2" style="width:16%;">Bunga</th>
					<th class="text-center align-middle" rowspan="2" style="width:8%;">Denda</th>
					<th class="text-center align-middle" rowspan="2" style="width:8%;">Titipan</th>
					<th class="text-center align-middle" rowspan="2" style="width:8%;">Lain - Lain</th>
				</tr>
				<tr>
					<th class="text-center" style="width:8%;">PA</th>
					<th class="text-center" style="width:8%;">PT</th>
					<th class="text-center" style="width:8%;">PA</th>
					<th class="text-center" style="width:8%;">PT</th>
				</tr>
			</thead>
			<tbody>
				@php $lua = null @endphp
				@forelse($jurnal as $k => $v)
					
					<tr class="text-center">
						<td class="text-center">
							{{$loop->iteration}}
						</td>
						<td class="text-left">
							<a href="{{route('kredit.show', ['id' => $v['morph_reference_id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'angsuran'])}}">
							{{$v['nomor_faktur']}}
							</a>
						</td>
						<!-- <td class="text-left">
							Angsuran Kredit Nomor {{$v['nomor_kredit']}}
						</td> -->
						<td class="text-right">
							{{$idr->formatMoneyTo($v['provisi'])}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo($v['administrasi'])}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo($v['legal'])}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo($v['pokok_pa'])}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo($v['pokok_pt'])}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo($v['bunga_pa'])}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo($v['bunga_pt'])}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo($v['denda'])}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo($v['titipan'])}}
						</td>
						<td class="text-right">
							{{$idr->formatMoneyTo($v['lain_lain'])}}
						</td>
					</tr>
				@empty
				@endforelse
				<tfoot>
					<tr>
						<td colspan="12">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="5" class="text-right">JUMLAH ANGSURAN JATUH TEMPO</td>
						<td colspan="2" class="text-right">{{$idr->formatMoneyTo($total_jt)}}</td>
						<td colspan="5" rowspan="5">
							<table style="width: 100%;height: 200px">
								<thead>
									<tr class="text-center">
										<th style="width: 33%">
											Dibuat
										</th>
										<th style="width: 33%">
											Dibukukan
										</th>
										<th style="width: 33%">
											Diperiksa
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
						<!-- <th>Dibuat</th> -->
					</tr>
					<tr>
						<td colspan="5" class="text-right">JUMLAH PELUNASAN DIPERCEPAT DAN PENURUNAN POKOK</td>
						<td colspan="2" class="text-right">{{$idr->formatMoneyTo($total_a)}}</td>
					<tr>
						<td colspan="5" class="text-right">TOTAL ANGSURAN</td>
						<td colspan="2" class="text-right">{{$idr->formatMoneyTo($total_jt + $total_a)}}</td>
					</tr>
					<tr>
						<td colspan="2" rowspan="2">{{$kantor_aktif['nama']}}<br/>
						<small>{{implode(' ', $kantor_aktif['alamat'])}}</small>
						</td>
						<td rowspan="2">SALDO TUTUP KAS HARI INI<br/> {{$today->adddays(1)->format('d/m/Y')}}</td>
						<td rowspan="2" class="text-right" colspan="2"><h3>{{$idr->formatMoneyTo($total + $p_total )}}</h3></td>
						<td colspan="2" rowspan="2"><i>Terbilang : {{ucwords($idr->terbilang(abs($total + $p_total) ))}} Rupiah</i></td>
					</tr>
				</tfoot>
			</tbody>
		</table>
	</div>
</body>
</html>