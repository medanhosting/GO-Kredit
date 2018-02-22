@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 text-center"><i class="fa fa-paper-plane mr-2"></i> PENGAJUAN</h5>
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
					<h5 class="py-2 pl-2 mb-0">&nbsp;&nbsp;PENGAJUAN KREDIT</h5>
				@endslot
				
				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<nav class="nav nav-tabs underline" id="myTab" role="tablist">
								@if(array_intersect($acl_menu['pengajuan.pengajuan.permohonan'], $scopes->scopes))
								<a class="nav-item nav-link {{$is_permohonan_tab}}" id="nav-permohonan-tab" data-toggle="tab" href="#nav-permohonan" role="tab" aria-controls="nav-permohonan" aria-selected="true">
									Permohonan <span class="badge badge-success">{{$permohonan->total()}}</span>
								</a>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Permohonan
									</a>
								</li>
								@endif
								@if(array_intersect($acl_menu['pengajuan.pengajuan.survei'], $scopes->scopes))
								<a class="nav-item nav-link {{$is_survei_tab}}" id="nav-survei-tab" data-toggle="tab" href="#nav-survei" role="tab" aria-controls="nav-survei" aria-selected="false">
									Survei <span class="badge badge-success">{{$survei->total()}}</span>
								</a>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Survei
									</a>
								</li>
								@endif
								@if(array_intersect($acl_menu['pengajuan.pengajuan.analisa'], $scopes->scopes))
								<a class="nav-item nav-link {{$is_analisa_tab}}" id="nav-analisa-tab" data-toggle="tab" href="#nav-analisa" role="tab" aria-controls="nav-analisa" aria-selected="false">
									Analisa <span class="badge badge-success">{{$analisa->total()}}</span>
								</a>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Analisa
									</a>
								</li>
								@endif
								@if(array_intersect($acl_menu['pengajuan.pengajuan.putusan'], $scopes->scopes))
								<a class="nav-item nav-link {{$is_putusan_tab}}" id="nav-putusan-tab" data-toggle="tab" href="#nav-putusan" role="tab" aria-controls="nav-putusan" aria-selected="false">
									Putusan <span class="badge badge-success">{{$putusan->total()}}</span>
								</a>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Putusan
									</a>
								</li>
								@endif
							</nav>
							<div class="tab-content" id="nav-tabContent">
								@if(array_intersect($acl_menu['pengajuan.pengajuan.permohonan'], $scopes->scopes))
								<div class="tab-pane fade {{$is_permohonan_tab}}" id="nav-permohonan" role="tabpanel" aria-labelledby="nav-permohonan-tab">
									@include('v2.pengajuan.table', ['data' => $permohonan, 's_pre' => 'permohonan'])
								</div>
								@else
								@endif
								@if(array_intersect($acl_menu['pengajuan.pengajuan.survei'], $scopes->scopes))
								<div class="tab-pane fade {{$is_survei_tab}}" id="nav-survei" role="tabpanel" aria-labelledby="nav-survei-tab">
									@include('v2.pengajuan.table', ['data' => $survei, 's_pre' => 'survei'])
								</div>
								@else
								@endif
								@if(array_intersect($acl_menu['pengajuan.pengajuan.analisa'], $scopes->scopes))
								<div class="tab-pane fade {{$is_analisa_tab}}" id="nav-analisa" role="tabpanel" aria-labelledby="nav-analisa-tab">
									@include('v2.pengajuan.table', ['data' => $analisa, 's_pre' => 'analisa'])
								</div>
								@else
								@endif
								@if(array_intersect($acl_menu['pengajuan.pengajuan.putusan'], $scopes->scopes))
								<div class="tab-pane fade {{$is_putusan_tab}}" id="nav-putusan" role="tabpanel" aria-labelledby="nav-putusan-tab">
									@include('v2.pengajuan.table', ['data' => $putusan, 's_pre' => 'putusan'])
								</div>
								@else
								@endif
							</div>
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