<div class="row">
	<div class="col">
		<h5 class="pb-4">Overview</h5>
	</div>
</div>
@isset ($permohonan['nasabah'])
	@foreach ($permohonan['nasabah'] as $k => $v)
		@if (($k != 'keluarga') && ($k != 'alamat') && ($k != 'is_ktp') && ($k != 'nik'))
			<div class="row">
				<div class="col-3">
					<p class="text-secondary text-capitalize">{{ str_replace('_', ' ', $k) }}</p>
				</div>
				<div class="col">
					<p class="{{ ($k != 'email') ? 'text-capitalize' : '' }}">{{ str_replace('_', ' ', $v) }}</p>
				</div>
			</div>
		@elseif ($k == 'nik')
			<div class="row">
				<div class="col-3">
					<p class="text-secondary text-uppercase">{{ str_replace('_', ' ', $k) }}</p>
				</div>
				<div class="col">
					<p class="text-capitalize">
						{{ str_replace('_', ' ', $v) }} 
						@isset ($permohonan['nasabah']['is_ktp'])
							&nbsp;<span class="badge badge-info">E-KTP</span>
						@endisset
					</p>
				</div>
			</div>
		@elseif ($k == 'alamat')
			<div class="row mb-3">
				<div class="col-3">
					<p class="text-secondary text-capitalize">{{ $k }}</p>
				</div>
				<div class="col">
					@isset ($v['alamat'])
						<p class="text-capitalize mb-1">{{ $v['alamat'] }}</p>
						<p class="text-capitalize mb-1">
							@isset ($v['rt'])
								RT {{ $v['rt'] }}
							@endisset

							@isset ($v['rw'])
								&nbsp;&#47;&nbsp; RW {{ $v['rw'] }}
							@endisset
						</p>
						<p class="text-capitalize mb-1">
							@isset ($v['regensi'])
								{{ $v['regensi'] }}	
							@endisset

							@isset ($v['provinsi'])
								&nbsp;&#45;&nbsp; {{ $v['provinsi'] }}
							@endisset
						</p>
					@endisset

					@empty ($v['alamat'])
						<p class="mb-1">Alamat belum diinputkan</p>
					@endempty
				</div>
			</div>
		@endif
	@endforeach
@endisset

@empty($permohonan)
	<div class="row">
		<div class="col">Maaf data nasabah belum diisi</div>
	</div>
@endempty