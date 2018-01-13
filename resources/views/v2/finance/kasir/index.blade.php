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
						@foreach($akun as $k => $v)
							<a class="nav-item nav-link" id="nav-{{$v['kode_akun']}}-tab" data-toggle="tab" href="#nav-{{$v['kode_akun']}}" role="tab" aria-controls="nav-{{$v['kode_akun']}}" aria-selected="true">{{$v['akun']}}</a>
						@endforeach
					</nav>
					<div class="tab-content" id="nav-tabContent">
						@foreach($akun as $k => $v)
						<div class="tab-pane fade" id="nav-{{$v['kode_akun']}}" role="tabpanel" aria-labelledby="nav-{{$v['kode_akun']}}-tab">
						</div>
						@endforeach
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