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
			<h5 class="text-uppercase">
				Jumlah Tunggakan &nbsp;<strong class="text-danger">{{ $idr->formatMoneyTo($tunggakan['tunggakan']) }}</strong>
			</h5>
		</div>
	</div>
	<hr/>
	<div class="clearfix">&nbsp;</div>

	@if($sp)
	<div class="row">
		<div class="col-sm-12">
			{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'nip_karyawan' => Auth::user()['nip'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'tagihan']), 'method' => 'PATCH']) !!}

				<h6 class="text-secondary">PENERIMAAN TAGIHAN</h6>

				<div class="row mb-1">
					<div class="col-2">
						<label class="text-uppercase">Nama Penerima</label>
					</div>
					<div class="col">
						{!! Form::text('penerima[nama]', $aktif['nasabah']['nama'], ['class' => 'form-control inline-edit border-input w-25 text-info pb-1', 'placeholder' => 'Nama penerima']) !!}
					</div>
				</div>

				<div class="row mb-1">
					<div class="col-2">
						<label class="text-uppercase">Tanggal</label>
					</div>
					<div class="col">
						{!! Form::text('tanggal', Carbon\Carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control inline-edit border-input w-25 text-info pb-1 mask-datetime', 'placeholder' => 'dd/mm/yyyy']) !!}
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-2">
						<label class="text-uppercase">Nominal</label>
					</div>
					<div class="col">
						{!! Form::text('nominal', $tunggakan['tunggakan'], ['class' => 'form-control mask-money inline-edit border-input w-25 text-info pb-1', 'placeholder' => 'Nominal'], true) !!}
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-2">
						<label class="text-uppercase">Diambil Dari</label>
					</div>
					<div class="col">
						{!! Form::select('nomor_perkiraan', $akun, null, ['class' => 'form-control text-info inline-edit text-right w-25']) !!}
					</div>
				</div>

				<div class="row mb-1">
					<div class="col offset-md-2">
						<button class="btn btn-success">Tandai Penagihan</button>
					</div>
				</div>
				
			{!! Form::close() !!}
		</div>
	</div>
	{{--  <hr/>  --}}
	<div class="clearfix">&nbsp;</div>
	@endif
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
					<th class="text-right">Total Tunggakan</th>
					<th class="text-right">Sisa Hutang</th>
					<th class="text-right">Surat Peringatan</th>
				</tr>
			</thead>
			<tbody>
				@foreach($riwayat_t as $k => $v)
				<tr>
					<td>{{$v['tanggal']}}</td>
					<td>
						{{Carbon\Carbon::createfromformat('d/m/Y H:i', $v['tanggal'])->diffForHumans()}}
					</td>
					<td class="text-right">
						{{$idr->formatMoneyTo($v['tunggakan'])}}
					</td>
					<td class="text-right">
						{{$idr->formatMoneyTo($v['sisa_hutang'])}}
					</td>
					<td class="text-right">
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
