<div class="col-auto col-md-12">
	{!! Form::vText('Jalan', $prefix.'[alamat]'.$suffix, $alamat['alamat'], ['class' => $class.' alamat form-control inline-edit text-secondary', 'placeholder' => 'JL. Adi Sucipto Gang 2 Nomor 11']) !!}
</div>
<div class="col-auto col-md-12">
	{!! Form::vText('RT', $prefix.'[rt]'.$suffix, $alamat['rt'], ['class' => $class.' rt form-control inline-edit mask-rt-rw text-secondary', 'placeholder' => '001']) !!}
</div>
<div class="col-auto col-md-12">
	{!! Form::vText('RW', $prefix.'[rw]'.$suffix, $alamat['rw'], ['class' => $class.' rw form-control inline-edit mask-rt-rw text-secondary', 'placeholder' => '002']) !!}
</div>
<div class="col-auto col-md-12">
	{!! Form::vText('Desa/Dusun', $prefix.'[kelurahan]'.$suffix, $alamat['kelurahan'], ['class' => $class.' kelurahan form-control inline-edit text-secondary', 'placeholder' => 'MERGAN']) !!}
</div>

<div class="col-auto col-md-12">
	<div class="row form-group">
		<div class="col-sm-4 text-right">
			{!! Form::label('', 'KEC', ['class' => 'text-uppercase mb-1']) !!}
		</div>
		<div class="col-sm-8">
			<select class="ajax-teritori-kecamatan custom-select {{$class}} kecamatan form-control inline-edit required" name="{{$prefix}}[kecamatan]{{$suffix}}" style="width:100%">
				<option value="{{$kecamatan}}">{{$kecamatan}}</option>
			</select>
		</div>
	</div>
</div>

<div class="col-auto col-md-12">
	<div class="row form-group">
		<div class="col-sm-4 text-right">
			{!! Form::label('', 'KOTA/KAB', ['class' => 'text-uppercase mb-1']) !!}
		</div>
		<div class="col-sm-8">
			<select class="ajax-teritori-kota custom-select {{$class}} kota form-control inline-edit required" name="{{$prefix}}[kota]{{$suffix}}" style="width:100%">
				<option value="{{$kota}}">{{$kota}}</option>
			</select>
		</div>
	</div>
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
						return {
							q: params.term // search term
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
