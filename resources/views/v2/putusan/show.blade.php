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
					<h5 class="py-2 pl-2 mb-0 float-left">
						<a href="{{route('putusan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}">
							<i class="fa fa-chevron-left"></i>
						</a>
						&nbsp;&nbsp;DETAIL PUTUSAN KREDIT
					</h5>
				@endslot
			
				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<!-- Nav tabs -->

							<ul class="nav nav-tabs underline" role="tablist">
								@if(array_intersect($acl_menu['pengajuan.putusan.setuju.legalitas'], $scopes->scopes))
								<li class="nav-item">
									<a class="nav-link {{ $is_active_realisasi }}" data-toggle="tab" id="realisasi-menu" role="tab" href="#realisasi-pane">
										Legalitas Realisasi
									</a>
								</li>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Legalitas Realisasi
									</a>
								</li>
								@endif
								@if(array_intersect($acl_menu['pengajuan.putusan.setuju.pencairan'], $scopes->scopes))
								<li class="nav-item">
									<a class="nav-link {{ $is_active_pencairan }}" data-toggle="tab" id="pencairan-menu" role="tab" href="#pencairan-pane">
										Bukti Realisasi
									</a>
								</li>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Bukti Realisasi
									</a>
								</li>
								@endif
								@if(array_intersect($acl_menu['pengajuan.putusan.setuju.setoran'], $scopes->scopes))
								<li class="nav-item">
									<a class="nav-link {{ $is_active_setoran }}" data-toggle="tab" id="setoran-menu" role="tab" href="#setoran-pane">
										Setoran Realisasi
									</a>
								</li>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Setoran Realisasi
									</a>
								</li>
								@endif
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<!-- Tab panes -->
							<div class="tab-content">
								@if(array_intersect($acl_menu['pengajuan.putusan.setuju.legalitas'], $scopes->scopes))
								{{-- realisasi --}}
								<div class="tab-pane {!! $is_active_realisasi !!}" id="realisasi-pane" role="tabpanel">
									<div class="clearfix">&nbsp;</div>
									<div class="row">
										<div class="col-3">
											<!-- sidebar -->
											@include('v2.putusan.legalitas.sidebar')
										</div>
										<div class="col">
											<!-- catatan -->
											@include('v2.putusan.legalitas.list')
										</div>
									</div>
								</div>
								@endif

								@if(array_intersect($acl_menu['pengajuan.putusan.setuju.pencairan'], $scopes->scopes))
								{{-- pencairan --}}
								<div class="tab-pane {!! $is_active_pencairan !!}" id="pencairan-pane" role="tabpanel">
									<div class="clearfix">&nbsp;</div>
									<div class="row">
										<div class="col-3">
											<!-- sidebar -->
											@include('v2.putusan.pencairan.sidebar')
										</div>
										<div class="col">
											<!-- catatan -->
											@include('v2.putusan.pencairan.bukti')
										</div>
									</div>
								</div>
								@endif

								@if(array_intersect($acl_menu['pengajuan.putusan.setuju.setoran'], $scopes->scopes))
								{{-- setoran --}}
								<div class="tab-pane {!! $is_active_setoran !!}" id="setoran-pane" role="tabpanel">
									<div class="clearfix">&nbsp;</div>
									<div class="row">
										<div class="col-3">
											<!-- sidebar -->
											@include('v2.putusan.setoran.sidebar')
										</div>
										<div class="col">
											<!-- catatan -->
											@include('v2.putusan.setoran.bukti')
										</div>
									</div>
								</div>
								@endif
							</div>
						</div>
					</div>
				</div>
			@endcomponent
		</div>
	</div>
	@include('v2.putusan.modal.konfirmasi_putusan')
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush
