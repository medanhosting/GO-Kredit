<div class="row">
	<div class="col">
		<h5 class="pb-4">Analisa</h5>
	</div>
</div>
{{-- isi data analisa seperti table dibawah --}}
 <table class="table table-sm table table-responsive">
	<thead class="thead-default">
		<tr>
			<th>Ke</th>
			<th>Petugas</th>
			<th>Tanggal</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		@foreach($analisa as $k => $v)
			<tr>
				<td>{{($k+1)}}</td>
				<td>{{$v['analis']['nama']}}</td>
				<td>{{$v['tanggal']}}</td>
				<td>
					<a href="#">
						<i class="fa fa-eye"></i>
					</a>
				</td>
			</tr>
		@endforeach
	</tbody>
</table> 

<h5 class="text-gray mb-4 pl-3">Analisa Kredit</h5>
@foreach($analisa as $k => $v)
	@foreach($v as $k2 => $v2)
		@if(!in_array($k2, ['id', 'pengajuan_id', 'analis', 'created_at', 'updated_at', 'deleted_at', 'tanggal']))
			<div class="row pl-3">
				<div class="col-3">
					<p class="text-secondary text-capitalize">{{ str_replace('_', ' ', $k2) }}</p>
				</div>
				<div class="col">
					<p class="text-capitalize">{{ str_replace('_', ' ', $v2) }}</p>
				</div>
			</div>
		@endif
	@endforeach
@endforeach