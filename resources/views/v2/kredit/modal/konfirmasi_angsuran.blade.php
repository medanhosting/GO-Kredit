@component ('bootstrap.modal', ['id' => 'konfirmasi-angsuran'])
	@slot ('title')
		Konfirmasi bayar Angsuran
	@endslot

	@slot ('body')
		<p>Untuk membayar angsuran kredit, harap mengisi password Anda!</p>

		{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password']) !!}
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Konfirmasi', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent