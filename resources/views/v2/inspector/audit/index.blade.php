@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-user-secret mr-2"></i> INSPECTOR</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.inspector.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0">&nbsp;&nbsp;AUDIT</h5>
				@endslot
				<div class="card-body">

					<form action="{{route('audit.index')}}" method="GET">
						<div class="row">
							<!-- CARI BERDASARKAN DOKUMEN -->
							<div class="col-sm-4">
								<label>Cari Tanggal</label>
								<div class="form-row">
									<div class='col'>{!! Form::bsText(null, 'q', null, ['placeholder' => 'mulai', 'class' => 'mask-date form-control']) !!}</div>
						 			<input type="hidden" name="kantor_aktif_id" value="{{$kantor_aktif_id}}">
								</div>
							</div>
							<div class="col-sm-2 pl-1">
								<label>&nbsp;</label><br/>
								<button class="btn btn-primary" type="submit">Go!</button>
							</div>
						</div>
					</form>

					<div class="float-right">
						{{ $audit->appends(array_merge(request()->all()))->links('vendor.pagination.default') }}
					</div>
					<div class="clearfix">&nbsp;</div>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Tanggal</th>
								<th>Bagian</th>
								<th>Data Awal</th>
								<th>Data Akhir</th>
								<th>Data Perubahan</th>
								<th>Karyawan</th>
							</tr>
						</thead>
						<tbody>
							@forelse($audit as $k => $v)
							<tr>
								<td>{{$v['tanggal']}}</td>
								<td>{{ucwords($v['domain'])}}</td>
								<td>
									@foreach($v['data_lama'] as $k2 => $v2)
										<p>{{ucwords(str_replace('_',' ',$k2))}} : {{$v2}}</p>
									@endforeach
								</td>
								<td>
									@foreach($v['data_baru'] as $k2 => $v2)
										<p>{{ucwords(str_replace('_',' ',$k2))}} : {{$v2}}</p>
									@endforeach
								</td>
								<td>
									@foreach($v['data_perubahan'] as $k2 => $v2)
										<p>{{ucwords(str_replace('_',' ',$k2))}} : {{$v2}}</p>
									@endforeach
								</td>
								<td>
									{{$v['karyawan']['nama']}}
								</td>
							</tr>
							@empty
							<tr>
								<td colspan="6">Tidak ada data</td>
							</tr>
							@endforelse
						</tbody>
					</table>
					<div class="clearfix">&nbsp;</div>
				</div>
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush
