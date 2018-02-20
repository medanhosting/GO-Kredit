@component('bootstrap.card')
	<div class="card-header bg-light p-1">
		<h5 class='mb-0 p-2'>
			<strong>BAYAR ANGSURAN</strong>
		</h5>
	</div>
	<div class="card-body">
		{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'nip_karyawan' => Auth::user()['nip'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'kolektor', 'sp_id' => null]), 'method' => 'PATCH']) !!}
		<div class="row mb-1">
			<div class="col-4">
				<label class="text-uppercase">Nama</label>
			</div>
			<div class="col-8">
				{!! Form::text('penerima[nama]', $aktif['nasabah']['nama'], ['class' => 'form-control inline-edit border-input text-info py-1', 'placeholder' => 'Nama penerima']) !!}
			</div>
		</div>

		<div class="row mb-1">
			<div class="col-4">
				<label class="text-uppercase">Tanggal</label>
			</div>
			<div class="col-8">
				{!! Form::text('tanggal', $today->format('d/m/Y H:i'), ['class' => 'form-control inline-edit border-input text-info py-1 mask-date', 'placeholder' => 'dd/mm/yyyy']) !!}
			</div>
		</div>

		<div class="row mb-1">
			<div class="col-4">
				<label class="text-uppercase">Nominal</label>
			</div>
			<div class="col-8">
				{!! Form::text('nominal', $tunggakan['tunggakan'], ['class' => 'form-control mask-money inline-edit border-input text-info py-1', 'placeholder' => 'Nominal'], true) !!}
			</div>
		</div>

		<div class="clearfix">&nbsp;</div>
		<div class="row">
			<div class="col text-right">
				<a href="#" class="btn btn-success" data-toggle="modal" data-target='#konfirmasi_penagihan'>Bayar</a>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>

		@include ('v2.kredit.modal.konfirmasi_penagihan')
		
		{!! Form::close() !!}
	</div>
@endcomponent