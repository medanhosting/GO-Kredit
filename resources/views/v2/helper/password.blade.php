@component ('bootstrap.modal', ['id' => 'require_password', 'form' => true, 'method' => 'patch', 'url' => '#'])
	@slot ('title')
		Lanjutkan Proses
	@endslot

	@slot ('body')
		<p>Untuk melanjutkan, harap mengisi password Anda!</p>

		{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password']) !!}
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent

@push('js')
	<script type="text/javascript">
		//MODAL PARSE DATA ATTRIBUTE//
		$("a.modal_password").on("click", parsingDataAttributeModalPassword);

		function parsingDataAttributeModalPassword(){
			$('#require_password').find('form').attr('action', $(this).attr("data-action"));
		}
	</script>
@endpush