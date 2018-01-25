@inject('idr', 'App\Service\UI\IDRTranslater')
<div class="clearfix">&nbsp;</div>

<h6 class="text-secondary">SURAT PERINGATAN</h6>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Tanggal</th>
			<th>Surat Peringatan</th>
			<th>Tanda Terima</th>
		</tr>
	</thead>
	<tbody>
		@foreach($suratperingatan as $k => $v)
		<tr>
			<td>{{$v['tanggal']}}</td>
			<td class="text-left">
				{{strtoupper(str_replace('_',' ',$v['tag']))}}
				<br/>
				<a href="{{ route('tunggakan.print', ['id' => $v['nomor_kredit'], 'kantor_aktif_id' => $kantor_aktif_id, 'sp_id' => $v['id']]) }}" target="_blank">Cetak {{ucwords(str_replace('_', ' ', $v['tag']))}}</a>
			</td>
			<td>
				@if(!$v['penagihan'])
					{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'nip_karyawan' => Auth::user()['nip'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'tagihan', 'sp_id' => $v['id']]), 'method' => 'PATCH']) !!}
					<div class="row mb-1">
						<div class="col-4">
							<label class="text-uppercase">Nama Penerima</label>
						</div>
						<div class="col-8">
							{!! Form::text('penerima[nama]', $aktif['nasabah']['nama'], ['class' => 'form-control inline-edit border-input text-info pb-1', 'placeholder' => 'Nama penerima']) !!}
						</div>
					</div>

					<div class="row mb-1">
						<div class="col-4">
							<label class="text-uppercase">Tanggal</label>
						</div>
						<div class="col-8">
							{!! Form::text('tanggal', Carbon\Carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control inline-edit border-input text-info pb-1 mask-datetime', 'placeholder' => 'dd/mm/yyyy']) !!}
						</div>
					</div>

					<div class="row mb-1">
						<div class="col-4">
							<label class="text-uppercase">Nominal</label>
						</div>
						<div class="col-8">
							{!! Form::text('nominal', $tunggakan['tunggakan'], ['class' => 'form-control mask-money inline-edit border-input text-info pb-1', 'placeholder' => 'Nominal'], true) !!}
						</div>
					</div>

					<div class="row mb-1">
						<div class="col-4">
							<label class="text-uppercase">Diambil Dari</label>
						</div>
						<div class="col-8">
							{!! Form::select('nomor_perkiraan', $a_tt, null, ['class' => 'form-control text-info inline-edit text-right']) !!}
						</div>
					</div>

					<div class="row mb-1">
						<div class="col text-right">
							<button class="btn btn-success">Tandai Penagihan</button>
						</div>
					</div>
					
				{!! Form::close() !!}
				@else
					<div class="row mb-1">
						<div class="col-4">
							<label class="text-uppercase">Nama Penerima</label>
						</div>
						<div class="col-8">
							{!! Form::label($v['penagihan']['penerima']['nama']) !!}
						</div>
					</div>

					<div class="row mb-1">
						<div class="col-4">
							<label class="text-uppercase">Tanggal</label>
						</div>
						<div class="col-8">
							{!! Form::label($v['penagihan']['tanggal']) !!}
						</div>
					</div>

					<div class="row mb-1">
						<div class="col-4">
							<label class="text-uppercase">Nominal</label>
						</div>
						<div class="col-8">
							{!! Form::label($idr->formatmoneyTo($v['penagihan']['pelunasan'] + $v['penagihan']['titipan'])) !!}
						</div>
					</div>

					<div class="row mb-1">
						<div class="col-4">
							<label class="text-uppercase">Kolektor</label>
						</div>
						<div class="col-8">
							{!! Form::label($v['penagihan']['karyawan']['nama']) !!}
						</div>
					</div>
				@endif
			</td>
		</tr>
		@endforeach
	</tbody>
</table>
