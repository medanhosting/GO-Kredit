@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="mx-5 px-5 text-center"><i class="fa fa-credit-card-alt"></i> KREDIT</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-3 mb-0">&nbsp;&nbsp;KREDIT</h5>
				@endslot
				
				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<nav class="nav nav-tabs underline" id="myTab" role="tablist">
								<a class="nav-item nav-link {{$is_aktif_tab}}" id="nav-aktif-tab" data-toggle="tab" href="#nav-aktif" role="tab" aria-controls="nav-aktif" aria-selected="true">Kredit Aktif</a>
							</nav>
							<div class="tab-content" id="nav-tabContent">
								<div class="tab-pane fade {{$is_aktif_tab}}" id="nav-aktif" role="tabpanel" aria-labelledby="nav-aktif-tab">
									@include('v2.kredit.table', ['data' => $aktif, 'pre' => 'aktif'])
								</div>
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