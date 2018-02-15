@component ('bootstrap.modal', ['id' => 'konfirmasi_permohonan_restitusi'])
	@slot ('title')
		KONFIRMASI PERMOHONAN RESITUSI
	@endslot

	@slot ('body')
		<p>Untuk melakukan permohonan Resitusi Denda, Silahkan isi password Anda.</p>

		{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password', 'class' => 'set-focus form-control']) !!}
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Konfirmasi', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent