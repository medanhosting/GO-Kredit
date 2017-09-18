<div class="row">
	<div class="col">
		<h5 class="pb-4">Keputusan</h5>
	</div>
</div>
{{-- isi data putusan seperti table dibawah --}}
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
		@foreach($putusan as $k => $v)
			<tr>
				<td>{{($k+1)}}</td>
				<td>{{$v['pembuat_keputusan']['nama']}}</td>
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

<h5 class="text-gray mb-4 pl-3">Putusan Komite</h5>
@foreach($putusan as $k => $v)
	@foreach($v as $k2 => $v2)
		@if(!in_array($k2, ['id', 'pengajuan_id', 'pembuat_keputusan', 'created_at', 'updated_at', 'deleted_at', 'tanggal']))
			@if(is_array($v2))
				@foreach($v2 as $k3 => $v3)
					@if(is_array($v2))
						<p class="text-secondary text-capitalize pl-3">{{ str_replace('_', ' ', $k2) }} {{ str_replace('_', ' ', $k3) }}</p>
						@foreach($v3 as $k4 => $v4)
							<div class="row pl-5">
								<div class="col-3">
									<p class="text-secondary text-capitalize">{{ str_replace('_', ' ', $k4) }}</p>
								</div>
								<div class="col">
									<p class="text-capitalize">{{ str_replace('_', ' ', $v4) }}</p>
								</div>
							</div>
						@endforeach
					@else
						<div class="row pl-5">
							<div class="col-3">
								<p class="text-secondary text-capitalize">{{ str_replace('_', ' ', $k3) }}</p>
							</div>
							<div class="col">
								<p class="text-capitalize">{{ str_replace('_', ' ', $v3) }}</p>
							</div>
						</div>
					@endif
				@endforeach
			@else
				<div class="row pl-3">
					<div class="col-3">
						<p class="text-secondary text-capitalize">{{ str_replace('_', ' ', $k2) }}</p>
					</div>
					<div class="col">
						<p class="text-capitalize">{{ str_replace('_', ' ', $v2) }}</p>
					</div>
				</div>
			@endif
		@endif
	@endforeach
@endforeach