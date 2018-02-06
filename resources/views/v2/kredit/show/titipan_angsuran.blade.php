@inject('carbon', 'Carbon\Carbon')

{!! Form::open(['url' => route('kredit.store', ['id' => $kredit_id]), 'method' => 'POST']) !!}
	@foreach(request()->all() as $k => $v)
		<input type="hidden" name="{{$k}}" value="{{$v}}">
	@endforeach
	@component('bootstrap.card')
		@slot('title') 
			<h5 class='text-left'>
				<strong>BAYAR ANGSURAN SEBAGIAN</strong>
			</h5>
		@endslot
		@slot('body')
			{!! Form::bsText('Tanggal', 'tanggal', $carbon::now()->format('d/m/Y H:i'), ['class' => 'form-control mask-date-time inline-edit text-info pb-0 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
			{!! Form::bsText('Nominal', 'nominal', null, ['class' => 'form-control mask-money inline-edit text-info pb-0 border-input', 'placeholder' => 'Rp 330.000'], true) !!}
			{!! Form::hidden('current', 'bayar_sebagian') !!}
			<a href="#" data-toggle="modal" data-target="#konfirmasi_bayar_angsuran_sebagian" class="btn btn-primary text-right">Bayar</a>
		@endslot
		@include('v2.kredit.modal.konfirmasi_bayar_angsuran_sebagian')
	@endcomponent
{!! Form::close() !!}	
