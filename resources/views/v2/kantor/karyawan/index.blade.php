@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-briefcase mr-2"></i> KANTOR</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3">
			@include('v2.kantor.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('pre')
					<h6 class="pt-4 pl-4">KARYAWAN</h6>
				@endslot
				@slot('body')
					<form action="{{route('karyawan.index')}}" method="GET">
						<div class="row">
							<div class="col-sm-3">
								<label>Cari Nama</label>
							 	@foreach(request()->all() as $k => $v)
							 		@if(!in_array($k, ['q','sort']))
								 		<input type="hidden" name="{{$k}}" value="{{$v}}">
							 		@endif
							 	@endforeach
								<input type="text" name="q" class="form-control w-100" placeholder="cari nama" value="{{request()->get('q')}}">
							</div>
							<!-- FILTER BERDASARKAN KANTOR -->
							<div class="col-sm-3">
								<label>Kantor Penempatan</label>
								<select class="form-control" name="kantor">
									<option value="semua">Semua Kantor</option>
									@foreach($kantor as $k2 => $v2)
									<option value="{{$v2['id']}}" @if(str_is(request()->get('kantor'), $v2['id'])) selected @endif>{{$v2['nama']}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-sm-2">
								<label>Urutkan</label>
								<!-- URUTKAN BERDASARKAN NAMA/TANGGAL -->
								<select class="form-control" name="sort">
									<option value="nama-asc" @if(str_is(request()->get('sort'), 'nama-asc')) selected @endif>Nama [A - Z]</option>
									<option value="nama-desc" @if(str_is(request()->get('sort'), 'nama-desc')) selected @endif>Nama [Z - A]</option>
								</select>
							</div>
							<div class="col-sm-2 pl-1">
								<label>&nbsp;</label><br/>
								<button class="btn btn-primary" type="submit">Go!</button>
							</div>
						</div>
					</form>
					<div class="clearfix">&nbsp;</div>
					<div class="float-right">
						{{ $karyawan->appends(array_merge(request()->all()))->links() }}
					</div>

					<table class="table table-bordered">
	  					<thead>
							<tr>
								<th>#</th>
								<th>NIP</th>
								<th>Nama</th>
								<th>Email</th>
								<th class="w-25">Alamat</th>
								<th>Penempatan</th>
							</tr>
		 				</thead>
	  					<tbody>
							@forelse($karyawan as $k => $v)
			  					<tr>
			    					<td>
										{{(($karyawan->currentPage() - 1) * $karyawan->perPage()) + $k + 1}}
									</td>
			    					<td>
										{{$v['nip']}} 
									</td>
			    					<td>
										{{$v['nama']}}
										<br/><i class="fa fa-phone"></i> {{$v['telepon']}}
									</td>
			    					<td>
										{{$v['email']}}
									</td>
			    					<td class="w-25">
										@foreach($v['alamat'] as $k2 => $v2)
											{{ucwords($k2)}} {{ucwords($v2)}}
										@endforeach
									</td>
			    					<td>
			    						@foreach($v->penempatan as $k2 => $v2)
											<span class="badge badge-success">
												{{ ucwords(str_replace('_', ' ', $v2['role'])) }} 
												{{ ucwords(str_replace('_', ' ', $v2['kantor']['nama'])) }}
											</span> <br/>
										@endforeach
									</td>
								</tr>
							@empty
			  					<tr>
			    					<td colspan="6">
										Data tidak tersedia
									</td>
								</tr>
		  					@endforelse
		 				</tbody>
					</table>
				@endslot
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush