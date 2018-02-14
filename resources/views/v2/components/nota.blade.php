<div class="row">
	<div class="col-6 text-left">
		<h3 class="mb-2">{{strtoupper($kantor_aktif['nama'])}}</h3>
		<ul class="list-unstyled fa-ul">
			<li>
				<i class="fa fa-building-o fa-li" style="margin-top: .2rem;"></i>
				{{ implode(' ', $kantor_aktif['alamat']) }}
			</li>
			<li>
				<i class="fa fa-phone fa-li" style="margin-top: .2rem;"></i>
				{{ $kantor_aktif['telepon'] }}
			</li>
		</ul>
	</div>
	<div class="col-6 text-right">
		{!! Form::vText(null, 'tanggal', isset($tanggal) ? $tanggal : date::now('d/m/Y'), ['class' => 'form-control mask-date inline-edit text-info pb-0 border-input', 'placeholder' => 'dd/mm/yyyy hh:mm'], true) !!}
	</div>
</div>
<div class="row">
	<div class="col text-center">
		<h4 class="mb-2">{{ $title }}</h4>
	</div>
</div>
<hr class="mt-1 mb-2" style="border-size: 2px;">
<div class="clearfix">&nbsp;</div>
<div class="row">
	<div class="col-12">
		<table class="table w-100">
			@foreach ($data as $k => $v)
				@if ($loop->first)
					<thead>
						<tr>
							@foreach ($v as $k2 => $v2)
								<th>{{ $v2 }}</th>
							@endforeach
						</tr>
					</thead>
					<tbody>
				@endif
				
				<tr>
					@foreach ($v as $k2 => $v2)
						<td>{{ $v2 }}</td>
					@endforeach
				</tr>
					
				@if ($loop->last)
					</tbody>
				@endif
			@endforeach
		</table>
	</div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>

<div class="row">
	<div class="col-12">
		<table class="table table-bordered w-100 mt-4">
			<thead class="thead-light">
				<tr>
					<th class="text-center p-2 w-25">Diperiksa</th>
					<th class="text-center p-2 w-25">Disetujui</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="padding: 35px;">&nbsp;</td>
					<td style="padding: 35px;">&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>