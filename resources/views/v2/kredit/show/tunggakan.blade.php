@inject('idr', 'App\Service\UI\IDRTranslater')
<div class="clearfix">&nbsp;</div>

@if($tunggakan)
@if($latest_pay)
<div class="row">
	<div class="col-sm-12">
		Pembayaran Terakhir Dilakukan Tanggal {{$latest_pay['tanggal']}}
	</div>
</div>
@endif
<div class="row">
	<div class="col-sm-12">
		Jumlah Tunggakan {{$idr->formatMoneyTo($tunggakan['tunggakan'])}}
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<a href="">Tandai Penagihan</a>
	</div>
</div>

<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-sm-12">
		<h6 class="text-secondary">SURAT PERINGATAN</h6>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Tanggal</th>
					<th>Surat Peringatan</th>
				</tr>
			</thead>
			<tbody>
				@foreach($tunggakan['suratperingatan'] as $k => $v)
				<tr>
					<td>{{$v['tanggal']}}</td>
					<td>
						<a href="">Cetak {{ucwords(str_replace('_', ' ', $v['tag']))}}</a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endif

@if(count($riwayat_t))
	<h6 class="text-secondary">RIWAYAT TUNGGAKAN</h6>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Tanggal</th>
				<th>Surat Peringatan</th>
			</tr>
		</thead>
		<tbody>
			@foreach($riwayat_t as $k => $v)
			<tr>
				<td>{{$v['tanggal']}}</td>
				<td>
					<a href="">Cetak {{ucwords(str_replace('_', ' ', $v['tag']))}}</a>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
@elseif(!count($riwayat_t) && !$tunggakan)
<div class="row">
	<div class="col-sm-12">
		Tidak ada tunggakan
	</div>
</div>
@endif