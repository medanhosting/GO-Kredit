@inject('idr', 'App\Service\UI\IDRTranslater')
@push('main')
	<div class="row justify-content-center">
		<div class="col-auto px-5 pt-2">
			<h5 class="h5 mx-5 px-5 d-flex text-center"><i class="fa fa-credit-card-alt mr-2"></i> KREDIT</h5>
			<hr>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-12 col-sm-auto text-center text-sm-right pt-sm-5 pb-3 sidebar-plain">
			@include('v2.kredit.base')
		</div>
		<div class="col">
			@component('bootstrap.card')
				@slot('header')
					<h5 class="py-2 pl-2 mb-0">&nbsp;&nbsp;REGISTER BUKTI TRANSAKSI</h5>
				@endslot

				<div class="card-body">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12">
							{{-- SEARCH --}}
							{!! Form::open(['method' => "GET"]) !!}
								@foreach(request()->all() as $k => $v)
									@if(!str_is($k, 'q'))
										<input type="hidden" name="{{$k}}" value="{{$v}}">
									@endif
								@endforeach
								<div class="form-row align-items-end">
									<div class='col-sm-2 order-1'>
										{!! Form::bsText('Cari Tanggal', 'q', null, ['placeholder' => 'sebelum tanggal', 'class' => 'mask-date form-control']) !!}
									</div>
									<div class='col-auto order-3'>
										<div class="form-group">
											<label for="">&nbsp;</label>
											{!! Form::bsSubmit('<i class="fa fa-search"></i>', ['class' => 'btn btn-primary']) !!}
										</div>
									</div>
								</div>
							{!! Form::close() !!}

							<div class="clearfix">&nbsp;</div>
							<table class="table table-bordered" style="font-size:10px;">
								<thead>
									<tr class="text-center">
										<th>WAKTU CETAK</th>
										<th>NASABAH</th>
										<th>NO. SPK</th>
										<th>NO. FAKTUR</th>
										<th>NOMINAL</th>
										<th>PETUGAS</th>
									</tr>
								</thead>
								<tbody>
									@forelse($registers as $k => $v)
										<tr class="text-center">
											<td class="text-center">
												{{$v['tanggal']}}
											</td>
											<td class="text-center">
												{{$v['notabayar']['nasabah']['nama']}}
											</td>
											<td class="text-center">
												{{$v['notabayar']['morph_reference_id']}}
											</td>
											<td class="text-center">
												{{$v['nomor_faktur']}}
											</td>
											<td class="text-center">
												{{$v['notabayar']['jumlah']}}
											</td>
											<td>
												{{$v['karyawan']['nama']}}
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="6" class="text-center">
												<p>Data tidak tersedia, silahkan pilih Koperasi/BPR lain</p>
											</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>
				@endcomponent
			</div>
		</div>
		@include('v2.kredit.modal.konfirmasi_tunggakan')
	</div>
@endpush

@push('submenu')
	@include('templates.submenu.submenu')
@endpush

@push('css')
@endpush