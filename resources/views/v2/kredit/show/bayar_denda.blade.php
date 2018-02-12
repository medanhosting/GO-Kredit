@inject('carbon', 'Carbon\Carbon')

{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'denda']), 'method' => 'PATCH']) !!}
	{!! Form::bsText('Tanggal', 'tanggal', $carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control mask-date inline-edit text-info py-1 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
	{!! Form::bsText('Nominal', 'nominal', null, ['class' => 'form-control mask-money inline-edit text-info pb-0 border-input', 'placeholder' => 'Rp 330.000'], true) !!}

	<div class="row mt-3 mb-3">
		<div class="col-12">
			<label class="text-uppercase">Disetor Ke</label>
		</div>
		<div class="col-12">
			{!! Form::select('nomor_perkiraan', $akun, null, ['class' => 'form-control custom-select inline-edit border-input text-info text-right']) !!}
		</div>
	</div>

	{!! Form::hidden('current', 'denda') !!}
	<div class="clearfix">&nbsp;</div>
	<a href="#" data-toggle="modal" data-target="#konfirmasi_denda" class="btn btn-primary text-right">Bayar</a>
	@include('v2.kredit.modal.konfirmasi_denda')
{!! Form::close() !!}	
