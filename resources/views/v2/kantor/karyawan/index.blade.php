@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-briefcase mr-2"></i> KANTOR</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.kantor.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('body')
					<nav class="nav nav-tabs" id="myTab" role="tablist">
						<a class="nav-item nav-link {{$is_karyawan_tab}}" id="nav-karyawan-tab" data-toggle="tab" href="#nav-karyawan" role="tab" aria-controls="nav-karyawan" aria-selected="true">Karyawan Aktif</a>
						<a class="nav-item nav-link {{$is_karyawan_baru_tab}}" id="nav-karyawan-baru-tab" data-toggle="tab" href="#nav-karyawan-baru" role="tab" aria-controls="nav-karyawan-baru" aria-selected="true">Karyawan Baru</a>
						<a class="nav-item nav-link {{$is_upload_karyawan_tab}}" id="nav-upload-karyawan-tab" data-toggle="tab" href="#nav-upload-karyawan" role="tab" aria-controls="nav-upload-karyawan" aria-selected="true">Upload Karyawan</a>
					</nav>
					<div class="tab-content" id="nav-tabContent">
						<div class="tab-pane fade {{$is_karyawan_tab}}" id="nav-karyawan" role="tabpanel" aria-labelledby="nav-karyawan-tab">
							@include('v2.kantor.karyawan.table')
						</div>
						<div class="tab-pane fade {{$is_karyawan_baru_tab}}" id="nav-karyawan-baru" role="tabpanel" aria-labelledby="nav-karyawan-baru-tab">
							@include('v2.kantor.karyawan.form')
						</div>
						<div class="tab-pane fade {{$is_upload_karyawan_tab}}" id="nav-upload-karyawan" role="tabpanel" aria-labelledby="nav-upload-karyawan-tab">
							@include('v2.kantor.karyawan.batch')
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