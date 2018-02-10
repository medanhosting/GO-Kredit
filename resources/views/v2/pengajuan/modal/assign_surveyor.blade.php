@component ('bootstrap.modal', ['id' => 'assign-survei', 'form' => true, 'method' => 'post', 'url' => route('pengajuan.assign', ['kantor_aktif_id' => $kantor_aktif['id'], 'status' => 'permohonan'])])
	@slot ('title')
		Assign Survei
	@endslot

	@slot ('body')
		<p>Untuk assign survei, harap melengkapi data berikut!</p>

		{!! Form::bsText('Tanggal', 'tanggal', $today->format('d/m/Y'), ['class' => 'form-control mask-date', 'placeholder' => 'dd/mm/yyyy'], true) !!}
		
		<div class="form-group">
			{!! Form::label('', 'SURVEYOR', ['class' => 'text-uppercase mb-1']) !!}
			<select class="ajax-karyawan custom-select form-control required" name="surveyor[nip][]" multiple="multiple" style="width:100%">
				<option value="">Pilih</option>
			</select>
		</div>
		{!! Form::bsPassword('password', 'password', ['placeholder' => 'Password']) !!}
	@endslot

	@slot ('footer')
		<a href="#" data-dismiss="modal" class="btn btn-link text-secondary">Batal</a>
		{!! Form::bsSubmit('Simpan', ['class' => 'btn btn-primary']) !!}
	@endslot
@endcomponent

@push('js')
	<script type="text/javascript">
		//ASSIGN SURVEYOR
		$(".ajax-karyawan").select2({
			ajax: {
				url: "{{route('manajemen.karyawan.ajax')}}",
				data: function (params) {
						return {
							q: params.term, // search term
							kantor_aktif_id: "{{$kantor_aktif['id']}}", // search term
							scope: 'survei'
						};
					},
				processResults: function (data, params) {
					return {
						results:  $.map(data, function (karyawan) {
							return {
								text: karyawan.orang.nama,
								id: karyawan.orang.nip
							}
						})
					};
				},
			}
		});
		
		//MODAL PARSE DATA ATTRIBUTE//
		$("a.modal_assign").on("click", parsingDataAttributeModalAssign);

		function parsingDataAttributeModalAssign(){
			$('#assign-survei').find('form').attr('action', $(this).attr("data-action"));
		}
	</script>
@endpush