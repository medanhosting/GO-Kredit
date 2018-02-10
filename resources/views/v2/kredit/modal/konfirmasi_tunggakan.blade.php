@component ('bootstrap.modal', ['id' => 'konfirmasi_tunggakan', 'form' => true, 'method' => 'PATCH'])
	@slot ('title')
		KONFIRMASI PASSWORD
	@endslot

	@slot ('body')
		<p id="body-modal"></p>

		{!! Form::bsText('Tanggal', 'tanggal', $today->format('d/m/Y'), ['class' => 'form-control mask-date', 'placeholder' => 'dd/mm/yyyy'], true) !!}
		
		{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password', 'class' => 'set-focus form-control']) !!}
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Konfirmasi', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent