@component ('bootstrap.modal', ['id' => 'konfirmasi', 'form' => true, 'method' => 'post'])
	@slot ('title')
		KONFIRMASI PASSWORD
	@endslot

	@slot ('body')
		<p id="body-modal"></p>
		{!! Form::hidden('kantor_aktif_id', request()->get('kantor_aktif_id')) !!}

		{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password', 'class' => 'set-focus form-control']) !!}
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Konfirmasi', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent