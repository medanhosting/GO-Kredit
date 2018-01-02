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
		{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'penerima[nama]' => $aktif['nasabah']['nama'], 'nip_karyawan' => Auth::user()['nip'], 'tanggal' => Carbon\Carbon::now()->format('d/m/Y H:i'), 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'tagihan', 'nominal' => 'Rp 4.000.000']), 'method' => 'PATCH']) !!}
			<button class="btn btn-success">Tandai Penagihan</button>
		{!! Form::close() !!}
	</div>
</div>
@endif

<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-sm-12">
		<h6 class="text-secondary">RIWAYAT TUNGGAKAN</h6>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Tanggal</th>
					<th>Sejak</th>
					<th>Total Tunggakan</th>
					<th>Sisa Hutang</th>
					<th>Surat Peringatan</th>
				</tr>
			</thead>
			<tbody>
				@foreach($riwayat_t as $k => $v)
				<tr>
					<td>{{$v['tanggal']}}</td>
					<td>
						{{Carbon\Carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->diffForHumans()}}
					</td>
					<td>
						{{$idr->formatMoneyTo($v['tunggakan'])}}
					</td>
					<td>
						{{$idr->formatMoneyTo($v['sisa_hutang'])}}
					</td>
					<td>
						@foreach($v['suratperingatan'] as $v0)
							<a href="">Cetak {{ucwords(str_replace('_', ' ', $v0['tag']))}}</a><br>
							<small>{{Carbon\Carbon::createfromformat('d/m/Y H:i', $v0['tanggal'])->diffForHumans()}}</small><br/>
						@endforeach
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
