<div class="col-auto col-md-10">
	{!! Form::bsText('Jalan', $prefix.'[alamat]'.$suffix, $alamat['alamat'], ['class' => 'alamat form-control '.$inline, 'placeholder' => 'JL. Adi Sucipto Gang 2 Nomor 11']) !!}
</div>
<div class="row ml-0 mr-0">
	<div class="col-auto col-md-5">
		{!! Form::bsText('RT', $prefix.'[rt]'.$suffix, $alamat['rt'], ['class' => 'rt form-control mask-rt-rw '.$inline, 'placeholder' => '001']) !!}
	</div>
	<div class="col-auto col-md-5">
		{!! Form::bsText('RW', $prefix.'[rw]'.$suffix, $alamat['rw'], ['class' => 'rw form-control mask-rt-rw '.$inline, 'placeholder' => '002']) !!}
	</div>
</div>

<div class="col-auto col-md-10">
	{!! Form::bsText('Desa/Dusun', $prefix.'[kelurahan]'.$suffix, $alamat['kelurahan'], ['class' => 'kelurahan form-control '.$inline, 'placeholder' => 'MERGAN']) !!}
</div>

<div class="col-auto col-md-10">
	<div class="form-group">
		{!! Form::label('', 'KECAMATAN', ['class' => 'text-uppercase mb-1']) !!}
		<select class="ajax-teritori-kecamatan custom-select {{$inline}} kecamatan form-control required" name="{{$prefix}}[kecamatan]{{$suffix}}" style="width:100%">
			<option value="{{$kecamatan}}">{{$kecamatan}}</option>
		</select>
	</div>
</div>

<div class="col-auto col-md-10">
		{!! Form::label('', 'KOTA/KABUPATEN', ['class' => 'text-uppercase mb-1']) !!}
	<select class="ajax-teritori-kota custom-select {{$inline}} kota form-control required" name="{{$prefix}}[kota]{{$suffix}}" style="width:100%">
		<option value="{{$kota}}">{{$kota}}</option>
	</select>
</div>

@push('js')
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

	<script type="text/javascript">
		$(".ajax-teritori-kecamatan").select2({
			ajax: {
				url: "{{route('distrik.index')}}",
				data: function (params) {
						return {
							q: params.term // search term
						};
					},
				processResults: function (data, params) {
					return {
						results:  $.map(data, function (kecamatan) {
							return {
								pusat: kecamatan.kota.nama,
								text: kecamatan.nama,
								id: kecamatan.nama
							}
						})
					};
				},
			}
		});

		$(".ajax-teritori-kota").select2({
			ajax: {
				url: "{{route('regensi.index')}}",
				data: function (params) {
				var kecamatan = $('.ajax-teritori-kecamatan').select2('data');
						return {
							q: kecamatan[0].pusat // search term
						};
					},
				processResults: function (data, params) {
					return {
						results:  $.map(data, function (kota) {
							return {
								text: kota.nama,
								id: kota.nama
							}
						})
					};
				},
			}
		});
	</script>
@endpush
