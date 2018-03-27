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
					<h5 class="py-2 pl-2 mb-0">&nbsp;&nbsp;LAPORAN TAGIHAN KOLEKTOR</h5>
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
										<th rowspan="2" class="align-middle">TANGGAL</th>
										<th rowspan="2" class="align-middle">NAMA</th>
										<th rowspan="2" class="align-middle">NO. SPK</th>
										<th colspan="2">NO BUKTI</th>
										<th rowspan="2" class="align-middle">NOMINAL</th>
									</tr>
									<tr class="text-center">
										<th>CETAK KOLEKTOR</th>
										<th>CETAK KASIR</th>
									</tr>
								</thead>
								<tbody>
									@php $prev = null @endphp
									@forelse($notabayar as $k => $v)
										@if($prev!=$v['karyawan']['nip'])
										<tr class="text-center">
											<td class="text-left bg-light" colspan="6">
												<strong>KOLEKTOR :</strong>
												{{$v['karyawan']['nama']}}
											</td>
										</tr>
										@php $prev = $v['karyawan']['nip'] @endphp
										@endif
										<tr class="text-center">
											<td class="text-center">
												{{$v['hari']}}
											</td>
											<td class="text-left">
												{{$v['nasabah']['nama']}}
											</td>
											<td class="text-center">
												{{$v['morph_reference_id']}}
											</td>
											<td class="text-center">
												{{$v['nomor_faktur']}}
											</td>
											<td class="text-center">
												@forelse($v['child'] as $k2 => $v2)
												<p>{{$v2['nomor_faktur']}}</p>
												@empty
												{!! Form::open(['url' => route('kredit.update', ['id' => $v['kredit']['id'], 'kantor_aktif_id' => $kantor_aktif['id']]), 'method' => 'PATCH']) !!}
													@foreach(request()->all() as $k2 => $v2)
														<input type="hidden" name="{{$k2}}" value="{{$v2}}">
													@endforeach
													<input type="hidden" name="nomor_faktur" value="{{$v['nomor_faktur']}}">
													<input type="hidden" name="current" value="penerimaan_kas_kolektor">

													{!! Form::bsSelect(null, 'nomor_perkiraan', $akun, null, ['class' => 'form-control custom-select inline-edit border-input text-info']) !!}
													<a href="#" class="btn btn-success" data-toggle="modal" data-target='#konfirmasi_tunggakan'>Terima</a>
													<div class="clearfix">&nbsp;</div>
													@include ('v2.kredit.modal.konfirmasi_tunggakan')
												{!! Form::close() !!}
												@endforelse
											</td>
											
											<td>
												{{$v['jumlah']}}
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