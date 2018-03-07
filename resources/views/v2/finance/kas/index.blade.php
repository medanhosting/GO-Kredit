@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 text-center"><i class="fa fa-line-chart mr-2"></i> KEUANGAN</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.finance.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0 float-left">&nbsp;&nbsp;BKK/BKM</h5>
				@endslot

				<div class="card-body">
					<div class="row">
						<div class="col-12 text-right">
							<a class="btn btn-primary" href="{{route('kas.create', ['kantor_aktif_id' => $kantor_aktif_id])}}">Bukti Baru</a>
						</div>
					</div>
					<div class="clearfix">&nbsp;</div>
					<div class="row">
						<div class="col-12">
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