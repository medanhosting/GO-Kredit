@component ('bootstrap.modal', ['id' => 'konfirmasi-denda'])
	@slot ('title')
		Konfirmasi pembayaran denda
	@endslot

	@slot ('body')
		<p>Untuk melanjutkan membayar dendan angsuran kredit, harap mengisi password Anda!</p>

		{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password']) !!}
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Konfirmasi', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent