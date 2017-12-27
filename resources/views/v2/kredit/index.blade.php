@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-credit-card-alt mr-2"></i> KREDIT</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('body')
					<nav class="nav nav-tabs" id="myTab" role="tablist">
						<a class="nav-item nav-link {{$is_aktif_tab}}" id="nav-aktif-tab" data-toggle="tab" href="#nav-aktif" role="tab" aria-controls="nav-aktif" aria-selected="true">Kredit Aktif</a>
					</nav>
					<div class="tab-content" id="nav-tabContent">
						<div class="tab-pane fade {{$is_aktif_tab}}" id="nav-aktif" role="tabpanel" aria-labelledby="nav-aktif-tab">
							@include('v2.kredit.table', ['data' => $aktif, 'pre' => 'aktif'])
						</div>
					</div>
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