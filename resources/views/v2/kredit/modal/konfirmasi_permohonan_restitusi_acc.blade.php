@component ('bootstrap.modal', ['id' => 'konfirmasi_permohonan_restitusi_acc'])
	@slot ('title')
		KONFIRMASI PERMOHONAN RESITUSI YANG DIAJUKAN
	@endslot

	@slot ('body')
		<p>Untuk melakukan Konfirmasi Permohonan Resitusi Denda, Silahkan isi password Anda.</p>

		{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password', 'class' => 'set-focus form-control']) !!}
		{!! Form::hidden('is_approved', null, ['id' => 'konfirm_approved']) !!}
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Konfirmasi', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent