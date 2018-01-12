@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-line-chart mr-2"></i> Keuangan</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.finance.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('body')
					<nav class="nav nav-tabs" id="myTab" role="tablist">
						<a class="nav-item nav-link {{$is_jurnal_bank_tab}}" id="nav-jurnal-bank-tab" data-toggle="tab" href="#nav-jurnal-bank" role="tab" aria-controls="nav-jurnal-bank" aria-selected="true">Jurnal Bank</a>
						<a class="nav-item nav-link {{$is_jurnal_kas_tab}}" id="nav-jurnal-kas-tab" data-toggle="tab" href="#nav-jurnal-kas" role="tab" aria-controls="nav-jurnal-kas" aria-selected="true">Jurnal Kas</a>
						<a class="nav-item nav-link {{$is_kas_harian_tab}}" id="nav-kas-harian-tab" data-toggle="tab" href="#nav-kas-harian" role="tab" aria-controls="nav-kas-harian" aria-selected="true">Kas Harian</a>
					</nav>
					<div class="tab-content" id="nav-tabContent">
						<div class="tab-pane fade {{$is_jurnal_bank_tab}}" id="nav-jurnal-bank" role="tabpanel" aria-labelledby="nav-jurnal-bank-tab">
						</div>
						<div class="tab-pane fade {{$is_jurnal_kas_tab}}" id="nav-jurnal-kas" role="tabpanel" aria-labelledby="nav-jurnal-kas-tab">
						</div>
						<div class="tab-pane fade {{$is_kas_harian_tab}}" id="nav-kas-harian" role="tabpanel" aria-labelledby="nav-kas-harian-tab">
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