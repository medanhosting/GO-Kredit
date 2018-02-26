@component ('bootstrap.modal', ['id' => 'konfirmasi-angsuran'])
	@slot ('title')
		Konfirmasi Bayar Angsuran
	@endslot

	@slot ('body')
		<p class="text-left">Untuk membayar angsuran kredit, harap mengisi password Anda!</p>

		<div class="text-left">
			{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password']) !!}
		</div>
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="text-left btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Konfirmasi', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent