@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-paper-plane mr-2"></i> PENGAJUAN</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.pengajuan.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0">
						<a href="{{route('pengajuan.index', ['kantor_aktif_id' => $kantor_aktif_id, 'current' => $permohonan['status_terakhir']['status']])}}">
							<i class="fa fa-chevron-left"></i> 
						</a>
						&nbsp;&nbsp;DETAIL PENGAJUAN
						<p class="float-right mr-3 text-secondary">
							STATUS &nbsp;&nbsp; 
							<span class="bg-info text-white text-uppercase pl-2 pr-2 pt-1 pb-1">
								{{ ucwords($permohonan['status_terakhir']['status']) }}
							</span>
						</p>
					</h5>
				@endslot
				
				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<!-- Nav tabs -->
							<ul class="nav nav-tabs underline" role="tablist">
								@if(array_intersect($acl_menu['pengajuan.pengajuan.*.permohonan'], $scopes->scopes))
								<li class="nav-item">
									<a class="nav-link {{ $is_active_permohonan }}" data-toggle="tab" id="permohonan-menu" role="tab" href="#">
										Permohonan
									</a>
								</li>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Permohonan
									</a>
								</li>
								@endif

								@if(array_intersect($acl_menu['pengajuan.pengajuan.*.survei'], $scopes->scopes))
								<li class="nav-item">
									<a class="nav-link {{ $is_active_survei }}" data-toggle="tab" id="survei-menu" role="tab" href="#">
										Survei
									</a>
								</li>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Survei
									</a>
								</li>
								@endif
								@if(array_intersect($acl_menu['pengajuan.pengajuan.*.analisa'], $scopes->scopes))
								<li class="nav-item">
									<a class="nav-link {{ $is_active_analisa }}" data-toggle="tab" id="analisa-menu" role="tab" href="#">
										Analisa
									</a>
								</li>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Analisa
									</a>
								</li>
								@endif
								@if(array_intersect($acl_menu['pengajuan.pengajuan.*.putusan'], $scopes->scopes))
								<li class="nav-item">
									<a class="nav-link {{ $is_active_putusan }}" data-toggle="tab" id="putusan-menu" role="tab" href="#">
										Putusan
									</a>
								</li>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Putusan
									</a>
								</li>
								@endif
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							@if(array_intersect($acl_menu['pengajuan.pengajuan.*.permohonan'], $scopes->scopes))
							{{-- PERMOHONAN --}}
							<div id="permohonan-panel" {!! ($is_active_permohonan) ? 'style=""' : 'style="display: none;"' !!}>
								<div class="clearfix">&nbsp;</div>
								<div class="row">
									<div class="col-3">
										@include('v2.pengajuan.permohonan.sidebar')
									</div>
									<div class="col-9">
										@if(str_is($permohonan['status_terakhir']['status'], 'permohonan'))
											@include('v2.pengajuan.permohonan.form')
										@else
											@include('v2.pengajuan.permohonan.list')
										@endif
									</div>
								</div>
								<div class="clearfix">&nbsp;</div>
							</div>
							@endif
							@if(array_intersect($acl_menu['pengajuan.pengajuan.*.survei'], $scopes->scopes))
							{{-- SURVEI --}}
							<div id="survei-panel" {!! ($is_active_survei) ? 'style=""' : 'style="display: none;"' !!}>
								<div class="clearfix">&nbsp;</div>
								<div class="row">
									@if(!str_is($permohonan['status_terakhir']['status'], 'permohonan'))
										<div class="col-3">
											@include('v2.pengajuan.survei.sidebar')
										</div>
										<div class="col-9">
											@if(!str_is($permohonan['status_terakhir']['status'], 'survei'))
												@include('v2.pengajuan.survei.list')
											@else
												@include('v2.pengajuan.survei.form')
											@endif
										</div>
									@else
										<div class="col-12">
											<p class="lead mt-3 text-center">
												<i class="fa fa-info-circle"></i> 
												Maaf data belum tersedia, pengajuan masih dalam status "<strong>{{ ucwords($permohonan['status_terakhir']['status']) }}</strong>"
											</p>
										</div>
									@endif	
								</div>
							</div>
							@else
							@endif
							@if(array_intersect($acl_menu['pengajuan.pengajuan.*.analisa'], $scopes->scopes))
							<div id="analisa-panel" {!! ($is_active_analisa) ? 'style=""' : 'style="display: none;"' !!}>
								<div class="clearfix">&nbsp;</div>
								<div class="row">
									@if(!in_array($permohonan['status_terakhir']['status'], ['permohonan', 'survei']))
										<div class="col-3">
											@include('v2.pengajuan.analisa.sidebar')
										</div>
										<div class="col-9">
											@if(!str_is($permohonan['status_terakhir']['status'], 'analisa'))
												@include('v2.pengajuan.analisa.list')
											@else
												@include('v2.pengajuan.analisa.form')
											@endif
										</div>
									@else
										<div class="col-12">
											<p class="lead mt-3 text-center">
												<i class="fa fa-info-circle"></i> 
												Maaf data belum tersedia, pengajuan masih dalam status "<strong>{{ ucwords($permohonan['status_terakhir']['status']) }}</strong>"
											</p>
										</div>
									@endif
								</div>
							</div>
							@else
							@endif
							@if(array_intersect($acl_menu['pengajuan.pengajuan.*.putusan'], $scopes->scopes))
							<div id="putusan-panel" {!! ($is_active_putusan) ? 'style=""' : 'style="display: none;"' !!}>
								<div class="clearfix">&nbsp;</div>
								<div class="row">
									@if(!in_array($permohonan['status_terakhir']['status'], ['permohonan', 'survei', 'analisa']))
										<div class="col-3">
											@include('v2.pengajuan.putusan.sidebar')
										</div>
										<div class="col-9">
											@if(!str_is($permohonan['status_terakhir']['status'], 'putusan'))
												@include('v2.pengajuan.putusan.list')
											@else
												@include('v2.pengajuan.putusan.form')
											@endif
										</div>
									@else
										<div class="col-12">
											<p class="lead mt-3 text-center">
												<i class="fa fa-info-circle"></i> 
												Maaf data belum tersedia, karena status masih dalam {{ ucwords($permohonan['status_terakhir']['status']) }}
											</p>
										</div>
									@endif
								</div>
							</div>
							@else
							@endif
							<div class="clearfix">&nbsp;</div>
						</div>
					</div>
				</div>
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush

@push('js')
	<script type="text/javascript">
		window.formInputMask.init();
		$("#permohonan-menu").click(function(){
			$("#permohonan-panel").fadeIn('fast');
			$("#survei-panel").fadeOut('fast');
			$("#analisa-panel").fadeOut('fast');
			$("#putusan-panel").fadeOut('fast');
		});
		$("#survei-menu").click(function(){
			$("#permohonan-panel").fadeOut('fast');
			$("#survei-panel").fadeIn('fast');
			$("#analisa-panel").fadeOut('fast');
			$("#putusan-panel").fadeOut('fast');
		});
		$("#analisa-menu").click(function(){
			$("#permohonan-panel").fadeOut('fast');
			$("#survei-panel").fadeOut('fast');
			$("#analisa-panel").fadeIn('fast');
			$("#putusan-panel").fadeOut('fast');
		});
		$("#putusan-menu").click(function(){
			$("#permohonan-panel").fadeOut('fast');
			$("#survei-panel").fadeOut('fast');
			$("#analisa-panel").fadeOut('fast');
			$("#putusan-panel").fadeIn('fast');
		});
	</script>
@endpush