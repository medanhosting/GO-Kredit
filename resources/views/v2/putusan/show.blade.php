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
						<a href="{{route('putusan.index', ['kantor_aktif_id' => $kantor_aktif_id])}}">
							<i class="fa fa-chevron-left"></i>
						</a>
						&nbsp;&nbsp;DETAIL PUTUSAN KREDIT
					</h5>
				@endslot
			
				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<nav class="nav nav-tabs underline" id="myTab" role="tablist">
								<a class="nav-item nav-link {{$is_legalitas_tab}}" id="nav-legalitas-tab" data-toggle="tab" href="#nav-legalitas" role="tab" aria-controls="nav-legalitas" aria-selected="true">Legalitas Realisasi</a>
								<a class="nav-item nav-link {{$is_bukti_pencairan_tab}}" id="nav-bukti-pencairan-tab" data-toggle="tab" href="#nav-bukti-pencairan" role="tab" aria-controls="nav-bukti-pencairan" aria-selected="true">Bukti Pencairan</a>
							</nav>
							<!-- Nav tabs -->
							<div class="tab-content">
								<div class="tab-pane fade {{$is_legalitas_tab}}" id="nav-legalitas" role="tabpanel">
									<div class="clearfix">&nbsp;</div>
									@include('v2.putusan.legalitas.list')
								</div>
								<div class="tab-pane fade {{$is_bukti_pencairan_tab}}" id="nav-bukti-pencairan" role="tabpanel">
									<div class="clearfix">&nbsp;</div>
									@include('v2.putusan.legalitas.bukti')
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