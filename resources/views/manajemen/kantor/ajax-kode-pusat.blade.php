<select class="kode-pusat-kantor form-control required" name="kantor_id">
	<option value="{{$kantor['pusat']['id']}}">{{$kantor['pusat']['nama']}}</option>
</select>

@push('js')
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

	<script type="text/javascript">
		$(".kode-pusat-kantor").select2({
			ajax: {
				url: "{{route('manajemen.kantor.ajax', ['kantor_aktif_id' => $kantor_aktif['id']])}}",
				data: function (params) {
						return {
							q: params.term // search term
						};
					},
				processResults: function (data, params) {
					return {
						results:  $.map(data, function (users) {
							return {
								text: users.nama,
								id: users.id
							}
						})
					};
				},
			}
		});
	</script>
@endpush
