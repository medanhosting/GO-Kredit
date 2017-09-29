@push('main')
	<div class="container bg-white bg-shadow p-4">
		<div class="row">
			<div class="col">
				<h4 class='mb-4 text-style text-secondary'>
					<span class="text-uppercase">Lokasi Survei</span> 
					<small><small>@if($survei->currentPage() > 1) Halaman {{$survei->currentPage()}} @endif</small></small>
				</h4>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
		<div class="row">
			<div class="col-3">
				<div class="card" style="border-radius:0px">
					<h6 class="card-header">CARI</h6>
					<div class="card-block">
						@php $prev_kota = null @endphp
						@foreach($kecamatan as $k => $v)
							@if($prev_kota != $v['kota'])
								@php $prev_kota = $v['kota'] @endphp
								<div class="row">
									<div class="col-sm-12">
										<p class="pt-3 pl-3"><label><strong>{{$v['kota']}}</strong></label></p>
									</div>
								</div>
							@endif
							<div class="row">
								<div class="col-sm-1"></div>
								<div class="col-sm-7">
									<p>
									@if($v['kecamatan']==request()->get('kecamatan') && $v['kota']==request()->get('kota'))
									<a href="{{route('pengajuan.survei.index', ['status' => 'survei', 'kantor_aktif_id' => $kantor_aktif['id']])}}">
									<i class="fa fa-check-square-o"></i>
									@else
									<a href="{{route('pengajuan.survei.index', ['status' => 'survei', 'kantor_aktif_id' => $kantor_aktif['id'], 'kecamatan' => $v['kecamatan'], 'kota' => $v['kota']])}}">
									<i class="fa fa-square-o"></i>
									@endif
									&nbsp;{{$v['kecamatan']}} </a> </p>
								</div>
								<div class="col-sm-3 text-right">
									<span class="badge badge-danger">{{$v['total']}}</span>
								</div>
								<div class="col-sm-1"></div>
							</div>
						@endforeach
					</div>
				</div>
			</div>
			<div class="col-9">
				<div class="row">
					@foreach($survei as $k => $v)
						<div class="col-sm-4">
							<div class="card" style="border-radius:0px">
								<div class="card-header" style="min-height:70px;">
									<h6 class="text-secondary">{{$v['nama']}}</h6>
									<h7><i class="fa fa-phone"></i>&nbsp;{{$v['telepon']}}</h7><br/>
								</div>
								<div class="card-body" style="min-height:150px;">
									<h7>{{$v['alamat']}}</h7>
								</div>
								<div class="card-footer">
									<h7>
										<a href="https://www.google.co.id/maps/search/{{$v['alamat']}}" target="__blank">
											<i class="fa fa-map-marker"></i>&nbsp;Temukan di Google Maps
										</a>
									</h7><br/>
									<h7>
										<a href="{{route('pengajuan.survei.show', ['id' => $v['id'], 'status' => 'survei', 'kantor_aktif_id' => $kantor_aktif['id']])}}">
											<i class="fa fa-edit"></i>&nbsp;Lengkapi Form Survei
										</a>
									</h7>
								</div>
							</div>
						<div class="clearfix">&nbsp;</div>
						</div>
					@endforeach
				</div>
				<div class="row text-right">
					<div class="col text-right">
						{{$survei->appends(request()->all())}}
					</div>
				</div>
			</div>
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push ('js')
@endpush