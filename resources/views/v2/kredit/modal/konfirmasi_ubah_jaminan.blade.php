@component ('bootstrap.modal', ['id' => 'konfirmasi_ubah_jaminan'])
	@slot ('title')
		KONFIRMASI PASSWORD
	@endslot

	@slot ('body')
		<p>Untuk mengubah data jaminan, harap mengisi password Anda!</p>

		{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password']) !!}
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Konfirmasi', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent