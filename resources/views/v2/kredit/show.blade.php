@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 text-center"><i class="fa fa-credit-card-alt mr-2"></i> KREDIT</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('pre')
					<h5 class="pt-4 pl-4 mb-0">
						<a href="{{route('kredit.index', ['kantor_aktif_id' => $kantor_aktif_id])}}">
							<i class="fa fa-chevron-left"></i> 
						</a>
						&nbsp;&nbsp;DETAIL KREDIT
					</h5>
				@endslot

				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							<!-- Nav tabs -->
							<ul class="nav nav-tabs underline" role="tablist">
								<li class="nav-item">
									<a class="nav-link {{$is_kredit_tab}}" data-toggle="tab" href="#kredit" role="tab">
										Kredit
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#angsuran" role="tab">
										Angsuran 
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{$is_tunggakan_tab}}" data-toggle="tab" href="#tunggakan" role="tab">
										Tunggakan 
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{$is_penagihan_tab}}" data-toggle="tab" href="#penagihan" role="tab">
										Penagihan 
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#jaminan" role="tab">
										Mutasi Jaminan 
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<!-- tab kredit -->
								<div class="tab-pane {{$is_kredit_tab}}" id="kredit" role="tabpanel">
									@include('v2.kredit.show.kredit')
								</div>
								<!-- tab angsuran -->
								<div class="tab-pane" id="angsuran" role="tabpanel">
									@include('v2.kredit.show.angsuran')
								</div>
								<!-- tab tunggakan -->
								<div class="tab-pane {{$is_tunggakan_tab}}" id="tunggakan" role="tabpanel">
									@include('v2.kredit.show.tunggakan')
								</div>
								<!-- tab penagihan -->
								<div class="tab-pane {{$is_penagihan_tab}}" id="penagihan" role="tabpanel">
									@include('v2.kredit.show.penagihan')
								</div>
								<!-- tab jaminan -->
								<div class="tab-pane" id="jaminan" role="tabpanel">
									@include('v2.kredit.show.jaminan')
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
