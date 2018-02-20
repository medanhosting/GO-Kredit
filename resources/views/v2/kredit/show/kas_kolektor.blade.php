@component('bootstrap.card')
	<div class="card-header bg-light p-1">
		<h5 class="font-weight-bold mb-0 p-2">PEMBAYARAN MASIH DI KOLEKTOR</h5>
	</div>
	<div class="card-body p-0 mb-0">
		<div class="table-responsive" style="max-height: 540px !important;">
			<table class="table table-hover table-bordered mb-0">
				<thead>
					<tr>
						<th class="text-center text-secondary">TANGGAL</th>
						<th class="text-left text-secondary">KOLEKTOR</th>
						<th class="text-right text-secondary">JUMLAH</th>
						<th class="text-center text-secondary">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@forelse($kas_kolektor as $k => $v)
						<tr>
							<td class="text-left">{{$v['hari']}}</td>
							<td class="text-left">{{$v['karyawan']['nama']}}</td>
							<td class="text-right text-style">{{$v['jumlah']}}</td>
							{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif['id']]), 'method' => 'PATCH']) !!}
							<td class="text-right">
								@foreach(request()->all() as $k2 => $v2)
									<input type="hidden" name="{{$k2}}" value="{{$v2}}">
								@endforeach
								<input type="hidden" name="nomor_faktur" value="{{$v['nomor_faktur']}}">
								<input type="hidden" name="current" value="penerimaan_kas_kolektor">

								{!! Form::bsSelect(null, 'nomor_perkiraan', $akun, null, ['class' => 'form-control custom-select inline-edit border-input text-info']) !!}
								<a href="#" class="btn btn-success" data-toggle="modal" data-target='#konfirmasi_tunggakan'>Terima</a>
								<div class="clearfix">&nbsp;</div>
							</td>
							@include ('v2.kredit.modal.konfirmasi_tunggakan')
							{!! Form::close() !!}
						</tr>
					@empty
						<tr>
							<td colspan="4" class="text-center">tidak ada data</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
@endcomponent