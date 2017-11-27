@inject('idr', 'App\Service\UI\IDRTranslater')
@php
	$hari 	= ['monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu', 'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu', 'sunday' => 'minggu'];
@endphp
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>KWITANSI PEMBAYARAN ANGSURAN</title>

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

		<!-- Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Fira+Sans" rel="stylesheet">

		<!-- Styles -->
		<style type="text/css">
			body{
				font-family: 'Fira Sans', sans-serif;
				font-size: 12px !important;
			}
		</style>
	</head>
	<body>
		<div class="container" style="width: 21cm;height: 29.7cm; ">
			<div class="clearfix">&nbsp;</div>
			
			<div class="row">
				<div class="col-xs-4">
				<h4>{{$kantor_aktif['nama']}}</h4>
				{{$kantor_aktif['alamat']['alamat']}}
				<br>
				Telepon {{$kantor_aktif['telepon']}}
				</div>
				<div class="col-xs-4">
					<h1>KWITANSI</h1>
				</div>
				<div class="col-xs-2">
				TANGGAL 
				<br>
				FAKTUR NO 
				<br>
				NO NASABAH 
				</div>
				<div class="col-xs-2">
				: {{is_null($angsuran['issued_at']) ? Carbon\Carbon::now()->format('d/m/Y H:i') : $angsuran['issued_at'] }}
				<br>
				: {{$angsuran['nomor_kredit']}} / {{$angsuran['id']}}
				<br>
				: {{$angsuran['kredit']['nasabah']['id']}}
				</div>
			</div>
			<div class="clearifx">&nbsp;</div>
			<div class="clearifx">&nbsp;</div>
			<div class="row">
				<div class="col-xs-2">
				DITERIMA DARI
				<br>SEJUMLAH UANG
				</div>
				<div class="col-xs-2">
				 : {{$angsuran['kredit']['nasabah']['nama']}}
				 <br>
				 : {{$idr->formatMoneyTo($angsuran['amount'])}}
				</div>
				<div class="col-xs-8 text-left">
				 <br>
 				({{ucwords($idr::terbilang($angsuran['amount']))}} Rupiah)
				</div>
			</div>
			<div class="clearifx">&nbsp;</div>
			<div class="clearifx">&nbsp;</div>
			<div class="row">
				<div class="col-xs-12">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th width="5">No</th>
								<th>Keterangan</th>
								<th class="text-right">Jumlah</th>
							</tr>
						</thead>
						<tbody>
							@foreach($angsuran->details as $k => $v)
							<tr>
								<td>{{$k+1}}</td>
								<td>{{$v['description']}}</td>
								<td class="text-right">{{$v['amount']}}</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2">Total</th>
								<th class="text-right">{{$idr->formatMoneyTo($angsuran['amount'])}}</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-2">
					TOTAL HUTANG<br>
					TOTAL ANGSURAN<br>
					SISA HUTANG<br><br>
					STATUS<br>
					JATUH TEMPO
				</div>
				<div class="col-xs-2">
					{{$t_hutang}}<br>
					{{$t_lunas}}<br>
					{{$s_hutang}}<br><br>
					{{(is_null($angsuran->paid_at) ? 'BELUM LUNAS' : 'LUNAS')}}<br>
					{{$angsuran->jatuh_tempo}}
				</div>
				<div class="col-xs-4">
				CATATAN
				<div style="border:1px solid #000">
					&nbsp;<br/>
					&nbsp;<br/>
					&nbsp;<br/>
					&nbsp;<br/>
				</div>
				</div>
				<div class="col-xs-4 text-right">
				{{is_null($angsuran['paid_at']) ? Carbon\Carbon::now()->format('d/m/Y H:i') : $angsuran['paid_at'] }}
				<br/>
				<br/>
				<br/>
				<br/>
				{{$kantor_aktif['nama']}}
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="clearfix">&nbsp;</div>
			<div class="clearfix">&nbsp;</div>
			<div class="clearfix">&nbsp;</div>
		</div>
	</body>
</html>