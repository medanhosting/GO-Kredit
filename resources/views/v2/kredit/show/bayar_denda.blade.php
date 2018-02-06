@inject('carbon', 'Carbon\Carbon')

{!! Form::open(['url' => route('kredit.update', ['id' => $aktif['id'], 'kantor_aktif_id' => $kantor_aktif_id, 'current' => 'denda']), 'method' => 'PATCH']) !!}
	@component('bootstrap.card')
		@slot('title') 
			<h5 class='text-left'>
				<strong>BAYAR DENDA</strong>
			</h5>
		@endslot
		@slot('body')
			{!! Form::bsText('Tanggal', 'tanggal', $carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control mask-date-time inline-edit text-info pb-0 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
			{!! Form::bsText('Nominal', 'nominal', null, ['class' => 'form-control mask-money inline-edit text-info pb-0 border-input', 'placeholder' => 'Rp 330.000'], true) !!}
			{!! Form::hidden('current', 'denda') !!}
			<a href="#" data-toggle="modal" data-target="#konfirmasi_denda" class="btn btn-primary text-right">Bayar</a>
		@endslot
	@endcomponent
	@include('v2.kredit.modal.konfirmasi_denda')
{!! Form::close() !!}	
