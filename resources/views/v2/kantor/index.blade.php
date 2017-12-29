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
				@slot('body')
					<nav class="nav nav-tabs" id="myTab" role="tablist">
						<a class="nav-item nav-link {{$is_kantor_tab}}" id="nav-kantor-tab" data-toggle="tab" href="#nav-kantor" role="tab" aria-controls="nav-kantor" aria-selected="true">Kantor Aktif</a>
						<a class="nav-item nav-link {{$is_kantor_baru_tab}}" id="nav-kantor-baru-tab" data-toggle="tab" href="#nav-kantor-baru" role="tab" aria-controls="nav-kantor-baru" aria-selected="true">Kantor Baru</a>
						<a class="nav-item nav-link {{$is_upload_kantor_tab}}" id="nav-upload-kantor-tab" data-toggle="tab" href="#nav-upload-kantor" role="tab" aria-controls="nav-upload-kantor" aria-selected="true">Upload Kantor</a>
					</nav>
					<div class="tab-content" id="nav-tabContent">
						<div class="tab-pane fade {{$is_kantor_tab}}" id="nav-kantor" role="tabpanel" aria-labelledby="nav-kantor-tab">
							@include('v2.kantor.table')
						</div>
						<div class="tab-pane fade {{$is_kantor_baru_tab}}" id="nav-kantor-baru" role="tabpanel" aria-labelledby="nav-kantor-baru-tab">
							@include('v2.kantor.form')
						</div>
						<div class="tab-pane fade {{$is_upload_kantor_tab}}" id="nav-upload-kantor" role="tabpanel" aria-labelledby="nav-upload-kantor-tab">
							@include('v2.kantor.batch')
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