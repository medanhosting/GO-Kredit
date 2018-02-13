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
					<h5 class="py-2 pl-3 mb-0">&nbsp;&nbsp;PUTUSAN KREDIT</h5>
				@endslot
				
				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<nav class="nav nav-tabs underline" id="myTab" role="tablist">
								@if(array_intersect($acl_menu['pengajuan.putusan.setuju'], $scopes->scopes))
								<a class="nav-item nav-link {{$is_setuju_tab}}" id="nav-setuju-tab" data-toggle="tab" href="#nav-setuju" role="tab" aria-controls="nav-setuju" aria-selected="true">
									Setuju <span class="badge badge-success">{{$setuju->total()}}</span>
								</a>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Setuju
									</a>
								</li>
								@endif
								@if(array_intersect($acl_menu['pengajuan.putusan.tolak'], $scopes->scopes))
								<a class="nav-item nav-link {{$is_tolak_tab}}" id="nav-tolak-tab" data-toggle="tab" href="#nav-tolak" role="tab" aria-controls="nav-tolak" aria-selected="false">
									Tolak <span class="badge badge-success">{{$tolak->total()}}</span>
								</a>
								@else
								<li class="nav-item ">
									<a class="nav-link disabled">
										Tolak
									</a>
								</li>
								@endif
							</nav>
							<div class="tab-content" id="nav-tabContent">
								@if(array_intersect($acl_menu['pengajuan.putusan.setuju'], $scopes->scopes))
								<div class="tab-pane fade {{$is_setuju_tab}}" id="nav-setuju" role="tabpanel" aria-labelledby="nav-setuju-tab">
									@include('v2.putusan.table', ['data' => $setuju, 's_pre' => 'setuju'])
								</div>
								@endif
								@if(array_intersect($acl_menu['pengajuan.putusan.tolak'], $scopes->scopes))
								<div class="tab-pane fade {{$is_tolak_tab}}" id="nav-tolak" role="tabpanel" aria-labelledby="nav-tolak-tab">
									@include('v2.putusan.table', ['data' => $tolak, 's_pre' => 'tolak'])
								</div>
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