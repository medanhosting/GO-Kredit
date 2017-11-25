@inject('idr', 'App\Service\UI\IDRTranslater')

@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<a href="">CETAK BUKTI BAYAR</a><br/>
				<a href="">HITUNG PELUNASAN</a>
			</div>
		</div>
	</div>
	<div class="clearifx">&nbsp;</div>
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col-sm-4">
			<h4>{{$kantor_aktif['nama']}}</h4>
			{{$kantor_aktif['alamat']['alamat']}}
			<br>
			Telepon {{$kantor_aktif['telepon']}}
			</div>
			<div class="col-sm-4">
				<h1>KWITANSI</h1>
			</div>
			<div class="col-sm-2">
			TANGGAL ANGSURAN 
			<br>
			FAKTUR NO 
			<br>
			NO PELANGGAN 
			</div>
			<div class="col-sm-2">
			: {{is_null($angsuran['paid_at']) ? Carbon\Carbon::now()->format('d/m/Y H:i') : $angsuran['paid_at'] }}
			<br>
			: {{$angsuran['nomor_kredit']}} / {{$angsuran['id']}}
			<br>
			: {{$angsuran['kredit']['nasabah']['id']}}
			</div>
		</div>
		<div class="clearifx">&nbsp;</div>
		<div class="clearifx">&nbsp;</div>
		<div class="row">
			<div class="col-sm-2">
			TELAH DITERIMA DARI
			<br>SEJUMLAH UANG
			</div>
			<div class="col-sm-2">
			 : {{$angsuran['kredit']['nasabah']['nama']}}
			 <br>
			 : {{$idr->formatMoneyTo($angsuran['amount'])}}
			</div>
			<div class="col-sm-8 text-left">
			{{ucwords($idr::terbilang($angsuran['amount']))}} Rupiah
			</div>
		</div>
		<div class="clearifx">&nbsp;</div>
		<div class="clearifx">&nbsp;</div>
		<div class="row">
			<div class="col-sm-12">
				<table class="table table-hover">
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
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2">
				TOTAL HUTANG<br>
				TOTAL ANGSURAN<br>
				SISA HUTANG<br><br>
				STATUS<br>
				JATUH TEMPO
			</div>
			<div class="col-sm-2">
				TOTAL HUTANG<br>
				TOTAL ANGSURAN<br>
				SISA HUTANG<br><br>
				STATUS<br>
				JATUH TEMPO
			</div>
			<div class="col-sm-4">
			PERHATIAN
			</div>
			<div class="col-sm-4">
			TANGGAL
			<br/>
			<br/>
			<br/>
			<br/>
			<br/>
			PEMILIK
			</div>
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push ('js')
@endpush