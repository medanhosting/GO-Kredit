@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-paper-plane mr-2"></i> PENGAJUAN</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('pre')
					<h6 class="pt-4 pl-4"><a href="{{route('kredit.index', ['kantor_aktif_id' => $kantor_aktif_id])}}"><i class="fa fa-angle-left"></i></a>&nbsp;&nbsp;DETAIL KREDIT</h6>
				@endslot
				@slot('body')
					@include('v2.pengajuan.putusan.list', ['putusan' => $realisasi])
					@include('v2.realisasi.legalitas.list')
				@endslot
			@endcomponent
		</div>
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush
