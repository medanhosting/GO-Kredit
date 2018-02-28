@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-gavel mr-2"></i> TEST</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.test.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-3 mb-0 float-left">&nbsp;&nbsp;PREDIKSI</h5>
				@endslot

				<div class="card-body">
					<form action="{{route('jp.predict', ['kantor_aktif_id' => $kantor_aktif_id, 'type' => 'jp'])}}" method="GET">
						<div class="row">
							<!-- CARI BERDASARKAN DOKUMEN -->
							<div class="col-sm-4">
								<label>Buat Jurnal Pagi Tanggal</label>
								<div class="form-row">
									<div class="col">
										{!! Form::bsText(null, 'q', null, ['placeholder' => 'd/m/Y', 'class' => 'mask-date form-control']) !!}
										{{Form::hidden('kantor_aktif_id', $kantor_aktif_id)}}
										{{Form::hidden('type', 'jp')}}
									</div>
									<div class="col-auto">
										<button class="btn btn-primary" type="submit">Go!</button>
									</div>
								</div>
							</div>
						</div>
					</form>

					<div class="clearfix">&nbsp;</div>
					<form action="{{route('jp.predict', ['kantor_aktif_id' => $kantor_aktif_id, 'type' => 'sp'])}}" method="GET">
						<div class="row">
							<!-- CARI BERDASARKAN DOKUMEN -->
							<div class="col-sm-4">
								<label>Buat SP</label>
								<div class="form-row">
									<div class="col">
										{!! Form::bsText(null, 'q', null, ['placeholder' => 'd/m/Y', 'class' => 'mask-date form-control']) !!}
										{{Form::hidden('kantor_aktif_id', $kantor_aktif_id)}}
										{{Form::hidden('type', 'sp')}}
									</div>
									<div class="col-auto">
										<button class="btn btn-primary" type="submit">Go!</button>
									</div>
								</div>
							</div>
						</div>
					</form>
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