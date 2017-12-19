@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-paper-plane mr-2"></i> PENGAJUAN</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3">
			@include('v2.pengajuan.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('body')
					<nav class="nav nav-tabs" id="myTab" role="tablist">
						<a class="nav-item nav-link {{$is_angsuran_tab}}" id="nav-angsuran-tab" data-toggle="tab" href="#nav-angsuran" role="tab" aria-controls="nav-angsuran" aria-selected="true">Kredit Angsuran [PA]</a>
						<a class="nav-item nav-link {{$is_musiman_tab}}" id="nav-musiman-tab" data-toggle="tab" href="#nav-musiman" role="tab" aria-controls="nav-musiman" aria-selected="false">Kredit Musiman [PT]</a>
					</nav>
					<div class="tab-content" id="nav-tabContent">
						<div class="tab-pane fade {{$is_angsuran_tab}}" id="nav-angsuran" role="tabpanel" aria-labelledby="nav-angsuran-tab">
							<div class="clearfix">&nbsp;</div>
							{!! Form::open(['url' => route('simulasi.index', ['mode' => 'pa', 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'GET']) !!}
								<div class="row">
									<div class="col-3">
										<input type="hidden" name="mode" value="pa">
										{!! Form::bsText('Pokok Pinjaman', 'pokok_pinjaman', null, ['class' => 'form-control mask-money', 'placeholder' => 'masukkan pokok pinjaman']) !!}
									</div>
									<div class="col-3">
										{!! Form::bsText('Kemampuan Angsur', 'kemampuan_angsur', null, ['class' => 'form-control mask-money', 'placeholder' => 'masukkan kemampuan angsur']) !!}
									</div>
									<div class="col-3">
										{!! Form::bsText('Bunga per Tahun', 'bunga_per_tahun', null, ['class' => 'form-control', 'placeholder' => 'masukkan bunga per tahun']) !!}
									</div>
									<div class="col-3" style="padding-top:23px;">
										{!! Form::bsSubmit('Hitung', ['class' => 'btn btn-primary float-left']) !!}
									</div>
								</div>
								{!! Form::hidden('kantor_aktif_id', $kantor_aktif['id']) !!}
							{!!Form::close()!!}
							@if(!empty($rincian) && $mode=='pa')
								@include('v2.simulasi.result')
							@endif
						</div>
						<div class="tab-pane fade {{$is_musiman_tab}}" id="nav-musiman" role="tabpanel" aria-labelledby="nav-musiman-tab">
							<div class="clearfix">&nbsp;</div>
							{!! Form::open(['url' => route('simulasi.index', ['mode' => 'pt', 'kantor_aktif_id' => $kantor_aktif_id]), 'method' => 'GET']) !!}
								<div class="row">
									<div class="col-3">
										<input type="hidden" name="mode" value="pt">
										{!! Form::bsText('Pokok Pinjaman', 'pokok_pinjaman', null, ['class' => 'form-control mask-money', 'placeholder' => 'masukkan pokok pinjaman']) !!}
									</div>
									<div class="col-3">
										{!! Form::bsText('Bunga per Tahun', 'bunga_per_tahun', null, ['class' => 'form-control', 'placeholder' => 'masukkan bunga per tahun']) !!}
									</div>
									<div class="col-3" style="padding-top:23px;">
										{!! Form::bsSubmit('Hitung', ['class' => 'btn btn-primary float-left']) !!}
									</div>
								</div>
								{!! Form::hidden('kantor_aktif_id', $kantor_aktif['id']) !!}
							{!!Form::close()!!}
							@if(!empty($rincian) && $mode=='pt')
								@include('v2.simulasi.result')
							@endif
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